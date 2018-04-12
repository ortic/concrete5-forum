<?php

namespace Concrete\Package\OrticForum\Controller\PageType;

use Concrete\Core\Page\Controller\PageTypeController;
use Concrete\Core\Routing\Redirect;
use Concrete\Package\OrticForum\Src\AuthenticationTrait;
use Core;
use User;

class Forum extends PageTypeController
{
    use AuthenticationTrait;

    /**
     * The ErrorList instance (available after the on_start method has been called).
     *
     * @var \Concrete\Core\Error\ErrorList\ErrorList|null
     */
    private $error;

    public function __construct(Page $c)
    {
        parent::__construct($c);

        $this->error = Core::make('error');
    }

    /**
     * View the topic listing.
     */
    public function view()
    {
        $forum = Core::make('ortic/forum');
        $topicList = $forum->getTopics();

        $pagination = $topicList->getPagination();
        $topics = $pagination->getCurrentPageResults();

        $this->set('topics', $topics);
        $this->set('pagination', $pagination);
        $this->set('user', new User());
        $this->set('forumTopicSubject', '');
        $this->set('forumTopicMessage', '');

        $this->render('forum', 'ortic_forum');
    }

    /**
     * Adds a new topic to the current forum (page).
     */
    public function writeTopic()
    {
        $token = Core::make('token');

        if ($this->getRequest()->isPost()) {
            if (!$token->validate('writeTopic')) {
                $this->error->add($token->getErrorMessage());
            }
            if (!$this->get('subject')) {
                $this->error->add(t('You must enter a subject'));
            }
            if (!$this->get('message')) {
                $this->error->add(t('You must enter a message'));
            }

            if ($this->error->has()) {
                $this->flash('forumError', $this->error);
                $this->flash('forumTopicSubject', $this->get('subject'));
                $this->flash('forumTopicMessage', $this->get('message'));
                return Redirect::to($this->action(''));
            }
            else {
                $forum = Core::make('ortic/forum');
                $topicPage = $forum->writeTopic($this->post('subject'), $this->post('message'));

                $this->flash('forumSuccess', t('Topic added'));
                return Redirect::to($topicPage->getCollectionLink());
            }
        }

        return Redirect::to($this->action(''));
    }

}