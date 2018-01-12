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
     * We handle all our magic methods through view(..). Since we handle various actions with a single method we
     * can't name our arguments very well.
     *
     * @param null $a
     * @param null $b
     * @param null $c
     * @param null $d
     */
    public function view($a = null, $b = null, $c = null, $d = null)
    {
        $parameters = func_get_args();
        $this->pageParameters = $parameters;
        $this->parameter = join('/', $parameters);
        $this->lastParameter = end($parameters);

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

    /**
     * Displays all messages from a single topic
     */
    protected function showTopic()
    {
        $forum = Core::make('ortic/forum');
        $topic = $forum->getTopic($this->parameters[0]);

        if (!$topic) {
            $this->replace('/page_not_found');
        }

        $messages = $forum->getMessages($topic);

        $this->set('topic', $topic);
        $this->set('messages', $messages);

        $this->render('topic', 'ortic_forum');
    }

    /**
     * Adds a message to an existing topic
     */
    protected function writeAnswer()
    {
        $forum = Core::make('ortic/forum');
        $topic = $forum->getTopic($this->parameters[0]);

        $forum = Core::make('ortic/forum');
        $forum->writeAnswer($topic, $this->post('message'));

        $this->showTopic();
    }

    /**
     * Adds a new topic to the current forum (page)
     */
    protected function writeTopic()
    {
        $forum = Core::make('ortic/forum');
        $forum->writeTopic($this->post('subject'), $this->post('message'));

        $this->showForum();
    }

    /**
     * Displays all topics of the current forum (page)
     */
    protected function showForum()
    {
        $forum = Core::make('ortic/forum');
        $topicList = $forum->getTopics();

        $pagination = $topicList->getPagination();
        $topics = $pagination->getCurrentPageResults();

        $this->set('topics', $topics);
        $this->set('pagination', $pagination);

        $this->render('forum', 'ortic_forum');
    }
}