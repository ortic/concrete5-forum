<?php

namespace Concrete\Package\OrticForum\Src\Repository;

use Concrete\Package\OrticForum\Src\Entity\ForumMessage;
use Concrete\Package\OrticForum\Src\ForumMessageList;
use Package;
use Page;
use Core;
use User;

class Forum
{
    /**
     * Returns a topic by the url slug
     *
     * @param string $slug
     * @return mixed
     */
    public function getTopic(string $slug)
    {
        $pkg = Package::getByHandle('ortic_forum');
        $em = $pkg->getEntityManager();
        $topic = $em->getRepository('Concrete\Package\OrticForum\Src\Entity\ForumMessage')->findOneBy(['slug' => $slug]);
        return $topic;
    }

    /**
     * Returns a list of messages that belong to the topic specified by $topic
     *
     * @param ForumMessage $topic
     * @return array
     */
    public function getMessages(ForumMessage $topic)
    {
        $messageList = new ForumMessageList();
        $messageList->filterByParent($topic->getID());
        $messageList->sortBy('dateCreated', 'asc');
        $messages = $messageList->getResults();

        return $messages;
    }

    /**
     * Adds a new answer for the current user to the topic specified by $topic.
     *
     * @param ForumMessage $topic
     * @param string $message
     */
    public function writeAnswer(ForumMessage $topic, string $message)
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
        $forumMessage->setParentId($topic->getID());

        $em->persist($forumMessage);
        $em->flush();
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
        $txt = Core::make('helper/text');

        $em = $pkg->getEntityManager();

        $user = new User();
        $page = Page::getCurrentPage();

        $object = new ForumMessage();
        $object->setSubject($subject);
        $object->setSlug($txt->urlify($subject)); // @TODO ensure this is unique
        $object->setMessage($message);
        $object->setDateCreated(new \DateTime);
        $object->setDateUpdated(new \DateTime);
        $object->setUser($user->getUserInfoObject()->getEntityObject());
        $object->setPageId($page->getCollectionId());

        $em->persist($object);
        $em->flush();
    }

    /**
     * Returns a list of all topics part of the current forum (page)
     * @return ForumMessageList
     */
    public function getTopics()
    {
        $page = Page::getCurrentPage();

        $topicList = new ForumMessageList();
        $topicList->filterByTopics();
        $topicList->filterByForumId($page->getCollectionId());
        $topicList->sortBy('dateCreated', 'desc');

        return $topicList;
    }
}