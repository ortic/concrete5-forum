<?php

namespace Concrete\Package\OrticForum\Src\Repository;

use Concrete\Core\Page\PageList;
use Concrete\Core\User\Group\Group;
use Concrete\Package\OrticForum\Src\Entity\ForumMessage;
use Concrete\Package\OrticForum\Src\ForumMessageList;
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
        $pkg = Package::getByHandle('ortic_forum');
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
        $pkg = Package::getByHandle('ortic_forum');
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
     * @param Page $topicPage
     * @param string $message
     */
    public function writeAnswer(Page $topicPage, string $message)
    {
        $pkg = Package::getByHandle('ortic_forum');
        $em = $pkg->getEntityManager();

        $user = new User();
        $page = Page::getCurrentPage();

        $forumMessage = new ForumMessage();
        $forumMessage->setMessage($message);
        $forumMessage->setDateCreated(new \DateTime);
        $forumMessage->setDateUpdated(new \DateTime);
        $forumMessage->setUser($user->getUserInfoObject()->getEntityObject());
        $forumMessage->setPageId($page->getCollectionId());

        $em->persist($forumMessage);
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
        $pkg = Package::getByHandle('ortic_forum');

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
        $pkg = Package::getByHandle('ortic_forum');

        $em = $pkg->getEntityManager();

        $em->remove($message);
        $em->flush();
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
     * Adds a new topic to the current forum (page)
     *
     * @param string $subject
     * @param string $message
     */
    public function writeTopic(string $subject, string $message)
    {
        $pkg = Package::getByHandle('ortic_forum');

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

        $em->persist($object);
        $em->flush();
    }

    /**
     * Returns a list of all topics part of the current forum (page)
     *
     * @return PageList
     */
    public function getTopics()
    {
        $page = Page::getCurrentPage();

        $topicList = new PageList();
        $topicList->filterByParentID($page->getCollectionId());
        $topicList->sortByDateModifiedDescending();

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

        $userIsOwner = $user->getUserId() == $message->user->getUserId();

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

        if ($message->getParentId()) {
            $topic = $this->getTopicById($message->getParentId());
            return $pageLink . '/' . $topic->getSlug() . '#message-' . $message->getId();
        } else {
            return $pageLink . '/' . $message->getSlug();
        }
    }
}