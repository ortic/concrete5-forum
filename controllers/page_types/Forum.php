<?php

namespace Concrete\Package\OrticForum\Controller\PageType;

use Concrete\Core\Page\Controller\PageTypeController;
use Core;
use User;

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
        $method = 'showTopic'; //default
        if ($this->lastParameter == '') {
            $method = 'showForum'; //show the forum if we have no params
        } else if ($this->getRequest()->isPost()) { //only call these if posted
            switch ($this->lastParameter) {
                case '_new':
                    $method = 'writeTopic';
                    break;
                case '_answer':
                    $method = 'writeAnswer';
                    break;
                case '_edit':
                    $method = 'updateMessage';
                    break;
            }
        }

        // call the appropriate method, passing parameters as parameters to the function
        call_user_func_array([$this, $method], $parameters);
    }

    /**
     * Displays all messages from a single topic
     * @param string $slug
     */
    protected function showTopic(string $slug)
    {
        $this->requireAsset('ortic/forum');

        $forum = Core::make('ortic/forum');
        $topic = $forum->getTopic($slug);

        if (!$topic) {
            $this->replace('/page_not_found');
        }

        $messages = $forum->getMessages($topic);
        array_unshift($messages, $topic);

        $this->set('topic', $topic);
        $this->set('messages', $messages);
        $this->set('user', new User());

        $this->render('topic', 'ortic_forum');
    }

    /**
     * Adds a message to an existing topic
     * @param string $slug
     */
    protected function writeAnswer(string $slug)
    {
        $forum = Core::make('ortic/forum');
        $topic = $forum->getTopic($slug);

        $forum = Core::make('ortic/forum');
        $forum->writeAnswer($topic, $this->post('message'));

        $this->showTopic($slug);
    }

    /**
     * Updates an existing message
     * @param string $slug
     * @param int $messageId
     */
    protected function updateMessage(string $slug, int $messageId)
    {
        $forum = Core::make('ortic/forum');
        $message = $forum->getMessage($messageId);
        $user = new User();

        if ($user->getUserId() != $message->user->getUserId()) {
            $this->showTopic($slug);
            return;
        }
        $forum->updateMessage($message, $this->post('message'));

        $this->showTopic($slug);
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