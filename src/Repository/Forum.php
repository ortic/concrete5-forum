<?php

namespace Concrete\Package\OrticForum\Src\Repository;

use Concrete\Core\Entity\File\Version;
use Concrete\Core\File\Set\Set as FileSet;
use Concrete\Core\File\Importer;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Page\PageList;
use Concrete\Core\User\Group\Group;
use Concrete\Package\OrticForum\Src\Entity\ForumMessage;
use Concrete\Package\OrticForum\Src\Entity\ForumMonitoring;
use Concrete\Package\OrticForum\Src\ForumMessageList;
use Concrete\Package\OrticForum\Src\ForumTopicList;
use Package;
use Core;
use Page;
use User;
use PageType;
use PageTemplate;

class Forum
{
    /**
     * Returns a topic by id
     *
     * @param int $id
     * @return mixed
     */
    public function getTopicById(int $id)
    {
        $pkg = Core::make(PackageService::class)->getByHandle('ortic_forum');
        $em = $pkg->getEntityManager();
        $topic = $em->getRepository('Concrete\Package\OrticForum\Src\Entity\ForumMessage')->find($id);
        return $topic;
    }

    /**
     * Returns a message by id
     *
     * @param int $id
     * @return mixed
     */
    public function getMessage(int $id)
    {
        $pkg = Core::make(PackageService::class)->getByHandle('ortic_forum');
        $em = $pkg->getEntityManager();
        $message = $em->getRepository('Concrete\Package\OrticForum\Src\Entity\ForumMessage')->find($id);

        return $message;
    }

    /**
     * Returns a list of messages that belong to the topic specified by $topic
     *
     * @param Page $topicPage
     * @return array
     */
    public function getMessages(Page $topicPage)
    {
        $messageList = new ForumMessageList();
        $messageList->filterByTopicId($topicPage->getCollectionID());
        $messageList->sortBy('dateCreated', 'asc');
        $messages = $messageList->getResults();

        return $messages;
    }

    /**
     * Adds a new answer for the current user to the topic specified by $topic.
     *
     * @param string $message
     * @param Version|null $attachment
     * @return ForumMessage
     */
    public function writeAnswer(string $message, Version $attachment = null)
    {
        $pkg = Core::make(PackageService::class)->getByHandle('ortic_forum');
        $em = $pkg->getEntityManager();

        $user = new User();
        $page = Page::getCurrentPage();

        $forumMessage = new ForumMessage();
        $forumMessage->setMessage($message);
        $forumMessage->setDateCreated(new \DateTime);
        $forumMessage->setDateUpdated(new \DateTime);
        $forumMessage->setUser($user->getUserInfoObject()->getEntityObject());
        $forumMessage->setPageId($page->getCollectionId());
        $forumMessage->setFirstMessage(0);
        $forumMessage->setLastMessage(1);

        if ($attachment) {
            $forumMessage->setAttachmentFileId($attachment->getFileID());
        }

        $em->persist($forumMessage);
        $em->flush();

        if ($attachment) {
            $tracker = Core::make('statistics/tracker');
            $tracker->track($forumMessage);
        }

        $this->updateLastMessage();

        // send notification about new message to subscribers
        $this->sentNotificationToTopicSubscribers($forumMessage);

        return $forumMessage;
    }

    /**
     * When a message is added or removed we have to mark the last message properly. This is needed to get the last
     * activity date of a topic without an ugly aggregation.
     */
    protected function updateLastMessage()
    {
        $pkg = Core::make(PackageService::class)->getByHandle('ortic_forum');
        $em = $pkg->getEntityManager();

        $topicPage = Page::getCurrentPage();

        $messageList = new ForumMessageList();

        $messageList->filterByTopicId($topicPage->getCollectionID());
        $messageList->sortBy('mID', 'asc');

        $messages = $messageList->getResults();

        // clear last message flag for all messages
        foreach ($messages as $message) {
            if ($message->getLastMessage()) {
                $message->setLastMessage(0);

                $em->persist($message);
            }
        }

        // make sure last message is actually marked as last message
        if ($message && !$message->getLastMessage()) {
            $message->setLastMessage(1);

            $em->persist($message);
        }

        $em->flush();
    }

    /**
     * Modifies an existing message
     *
     * @param ForumMessage $message
     * @param string $messageTxt
     */
    public function updateMessage(ForumMessage $message, string $messageTxt)
    {
        $pkg = Core::make(PackageService::class)->getByHandle('ortic_forum');

        $em = $pkg->getEntityManager();

        $message->setMessage($messageTxt);
        $message->setDateUpdated(new \DateTime);

        $em->persist($message);
        $em->flush();
    }

    /**
     * Deletes an existing message
     *
     * @param ForumMessage $message
     */
    public function deleteMessage(ForumMessage $message)
    {
        $pkg = Core::make(PackageService::class)->getByHandle('ortic_forum');

        // remove message/file from usage tracker
        $tracker = Core::make('statistics/tracker');
        $tracker->forget($message);

        // delete message
        $em = $pkg->getEntityManager();

        $em->remove($message);
        $em->flush();

        $this->updateLastMessage();
    }

    /**
     * Truncates a given string at a specified length
     *
     * @param $value
     * @param int $limit
     * @param string $end
     * @return string
     */
    public function limitString($value, $limit = 150, $end = '...')
    {
        if (mb_strwidth($value, 'UTF-8') <= $limit) {
            return $value;
        }

        return rtrim(mb_strimwidth($value, 0, $limit, '', 'UTF-8')) . $end;
    }

    /**
     * Uploads a file to the concrete5 file manager. All files will be added to the set defined by
     * ortic_forum.attachment_fileset_name. Throws exception if there's a problem.
     *
     * @param array $attachment
     * @return Version;
     * @throws \Exception
     */
    public function uploadAttachment(array $attachment)
    {
        $file = $attachment['tmp_name'];
        $filename = $attachment['name'];
        if ($filename) {
            $importer = new Importer();
            $attachmentResult = $importer->import($file, $filename);

            if (is_int($attachmentResult)) {
                $errorMessage = Importer::getErrorMessage($attachmentResult);
                throw new \Exception($errorMessage);
            }

            // attachment file to file set
            $config = Core::make('ortic/forum/config');
            $attachmentFilesetName = $config->get('ortic_forum.attachment_fileset_name');
            if ($attachmentFilesetName) {
                $fileSet = FileSet::createAndGetSet($attachmentFilesetName, FileSet::TYPE_PUBLIC);
                $fileSet->addFileToSet($attachmentResult);
            }

            return $attachmentResult;
        }
    }

    /**
     * Adds a new topic to the current forum (page)
     *
     * @param string $subject
     * @param string $message
     * @param Version $attachment
     * @return \Concrete\Core\Page\Page
     */
    public function writeTopic(string $subject, string $message, Version $attachment = null)
    {
        $pkg = Core::make(PackageService::class)->getByHandle('ortic_forum');

        // create new sub page
        $currentPage = Page::getCurrentPage();

        $pageType = PageType::getByHandle('forum_topic');
        $template = PageTemplate::getByHandle('forum_topic');

        $topicPage = $currentPage->add($pageType, array(
            'cName' => $subject,
            'cDescription' => $this->limitString($message),
        ), $template);

        $em = $pkg->getEntityManager();

        $user = new User();

        // add forum message
        $object = new ForumMessage();
        $object->setMessage($message);
        $object->setDateCreated(new \DateTime);
        $object->setDateUpdated(new \DateTime);
        $object->setUser($user->getUserInfoObject()->getEntityObject());
        $object->setPageId($topicPage->getCollectionId());
        $object->setLastMessage(1);
        $object->setFirstMessage(1);

        if ($attachment) {
            $object->setAttachmentFileId($attachment->getFileID());
        }

        $em->persist($object);
        $em->flush();

        if ($attachment) {
            $tracker = Core::make('statistics/tracker');
            $tracker->track($object);
        }

        return $topicPage;
    }

    /**
     * Returns a list of all topics part of the current forum (page)
     *
     * @return PageList
     */
    public function getTopics()
    {
        $page = Page::getCurrentPage();

        $topicList = new ForumTopicList();
        $topicList->filterByParentID($page->getCollectionId());
        $topicList->sortByLastActivityDate('desc');

        return $topicList;
    }

    /**
     * Returns true if one of these conditions is true:
     * - message is owned by current user
     * - current user is in admin group defined in config/ortic_forum.php
     * - current user is super admin
     *
     * @param ForumMessage $message
     * @return bool
     */
    public function canEditMessage(ForumMessage $message)
    {
        $user = new User();

        $userIsOwner = $user && $message->user && $user->getUserId() == $message->user->getUserId();

        $config = Core::make('ortic/forum/config');
        $adminGroupName = $config->get('ortic_forum.admin_group');
        $adminGroup = Group::getByName($adminGroupName);

        $userInAdminGroup = $user->inGroup($adminGroup);

        return $userIsOwner || $userInAdminGroup || $user->isSuperUser();
    }

    /**
     * Returns a link to the topic or messages specified by $message
     *
     * @param ForumMessage $message
     * @return string
     */
    public function getLink(ForumMessage $message)
    {
        $page = Page::getByID($message->getPageId());
        $pageLink = $page->getCollectionLink();

        return $pageLink . '#messsage-' . $message->getID();
    }

    /**
     * Subscribe the current user to the topic the message belongs to
     *
     * @param ForumMessage $message
     */
    public function subscribeForTopicChanges(ForumMessage $message)
    {
        $pkg = Core::make(PackageService::class)->getByHandle('ortic_forum');
        $em = $pkg->getEntityManager();

        $topicMonitor = new ForumMonitoring();
        $topicMonitor->setPageId($message->getPageId());
        $topicMonitor->setUser((new User())->getUserInfoObject()->getEntityObject());

        $em->persist($topicMonitor);
        $em->flush();
    }

    /**
     * Unsubscribe the current user to the topic the message belongs to
     *
     * @param ForumMessage $message
     */
    public function unsubscribeFromTopicChanges(ForumMessage $message)
    {
        $pkg = Core::make(PackageService::class)->getByHandle('ortic_forum');
        $em = $pkg->getEntityManager();

        $topicMonitor = $em
            ->getRepository('Concrete\Package\OrticForum\Src\Entity\ForumMonitoring')
            ->findOneBy(['pageId' => $message->getPageId(), 'user' => (new User())->getUserID()]);

        if ($topicMonitor) {
            $em->remove($topicMonitor);
            $em->flush();
        }
    }

    /**
     * Send notifications to subscribers of the topic that the message belongs to
     *
     * @param ForumMessage $message
     * @throws \Exception
     */
    public function sentNotificationToTopicSubscribers(ForumMessage $message)
    {
        $pkg = Core::make(PackageService::class)->getByHandle('ortic_forum');
        $em = $pkg->getEntityManager();

        $currentUser = new User();

        $query = $em->createQuery('SELECT m FROM \Concrete\Package\OrticForum\Src\Entity\ForumMonitoring m WHERE m.pageId = :pageId AND m.user != :user');
        $query->setParameter('pageId', $message->getPageId());
        $query->setParameter('user', $currentUser->getUserID());
        $topicSubscribers = $query->getResult();

        $topicPage = Page::getByID($message->getPageId());

        foreach ($topicSubscribers as $topicSubscriber) {
            $topicSubscriberUser = $topicSubscriber->getUser();

            $mh = Core::make('mail');
            $mh->to($topicSubscriberUser->getUserEmail());
            $mh->addParameter('message', $message);
            $mh->addParameter('topicPage', $topicPage);
            $mh->addParameter('currentUser', $currentUser);

            $mh->load('new_answer', 'ortic_forum');
            $mh->sendMail();
        }
    }
}