<?php

namespace Concrete\Package\OrticForum\Controller\PageType;

use Concrete\Core\Page\Controller\PageTypeController;
use Concrete\Package\OrticForum\Src\Entity\ForumMessage;
use Concrete\Package\OrticForum\Src\ForumMessageList;
use User;
use Page;
use Core;
use Package;

class Forum extends PageTypeController
{
    /** @var string the url part below the current page */
    protected $parameter;

    /** @var array the url sections below the current page */
    protected $pageParameters;

    /** @var string the last url part below the current page */
    protected $lastParameter;

    /**
     * Override core controller validation to make it possible to use pretty urls
     * as methods of our page type controller.
     *
     * By return true concrete5 will think of anything below a page of this type
     * as valid and thus invoke this controller.
     *
     * @param $action
     * @param array $parameters
     * @return bool
     */
    public function isValidControllerTask($action, $parameters = array())
    {
        $this->pageParameters = $parameters;
        $this->parameter = join('/', $parameters);
        $this->lastParameter = end($parameters);

        return true;
    }

    public function view()
    {
        switch ($this->lastParameter) {
            case '_new':
                $this->writeTopic();
                break;
            case '_answer':
                $this->writeAnswer();
                break;
            case '':
                $this->showForum();
                break;
            default:
                $this->showTopic();
                break;
        }
    }

    protected function showTopic()
    {
        $pkg = Package::getByHandle('ortic_forum');
        $em = $pkg->getEntityManager();
        $topic = $em->getRepository('Concrete\Package\OrticForum\Src\Entity\ForumMessage')->findOneBy(['slug' => $this->parameters[0]]);

        if (!$topic) {
            $this->replace('/page_not_found');
        }

        $messageList = new ForumMessageList();
        $messageList->filterByParent($topic->getID());
        $messages = $messageList->getResults();

        $this->set('topic', $topic);
        $this->set('messages', $messages);

        $this->render('topic', 'ortic_forum');
    }

    protected function writeAnswer() {
        $pkg = Package::getByHandle('ortic_forum');
        $em = $pkg->getEntityManager();
        $topic = $em->getRepository('Concrete\Package\OrticForum\Src\Entity\ForumMessage')->findOneBy(['slug' => $this->parameters[0]]);

        $user = new User();
        $page = Page::getCurrentPage();

        $object = new ForumMessage();
        $object->setSubject($this->post('subject'));
        $object->setParentId($topic->getID());
        $object->setMessage($this->post('message'));
        $object->setDateCreated(new \DateTime);
        $object->setDateUpdated(new \DateTime);
        $object->setUserId($user->getUserId());
        $object->setPageId($page->getCollectionId());

        $em->persist($object);
        $em->flush();

        $this->showTopic();
    }

    protected function writeTopic()
    {
        $pkg = Package::getByHandle('ortic_forum');
        $txt = Core::make('helper/text');

        $em = $pkg->getEntityManager();

        $user = new User();
        $page = Page::getCurrentPage();

        $object = new ForumMessage();
        $object->setSubject($this->post('subject'));
        $object->setSlug($txt->urlify($this->post('subject'))); // @TODO ensure this is unique
        $object->setMessage($this->post('message'));
        $object->setDateCreated(new \DateTime);
        $object->setDateUpdated(new \DateTime);
        $object->setUserId($user->getUserId());
        $object->setPageId($page->getCollectionId());

        $em->persist($object);
        $em->flush();

        $this->showForum();
    }

    protected function showForum()
    {
        $messages = new ForumMessageList();
        $messages->filterByTopics();

        $pagination = $messages->getPagination();
        $results = $pagination->getCurrentPageResults();

        $this->set('results', $results);
        $this->set('pagination', $pagination);

        $this->render('forum', 'ortic_forum');
    }
}