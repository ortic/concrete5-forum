<?php

namespace Concrete\Package\OrticForum\Controller\PageType;

use Concrete\Core\Page\Controller\PageTypeController;
use Concrete\Core\Routing\Redirect;
use Core;
use User;

class Forum extends PageTypeController
{
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

        $this->render('forum', 'ortic_forum');
    }

    /**
     * Adds a new topic to the current forum (page).
     */
    public function writeTopic()
    {
        $token = Core::make('token');

        if ($this->getRequest()->isPost()) {
            if ($token->validate('writeTopic')) {
                $forum = Core::make('ortic/forum');
                $topicPage = $forum->writeTopic($this->post('subject'), $this->post('message'));

                $this->flash('forumSuccess', t('Topic added'));
                return Redirect::to($topicPage->getCollectionLink());

            } else {
                $this->flash('forumError', $token->getErrorMessage());
                return Redirect::to($this->action(''));
            }
        }

        return Redirect::to($this->action(''));
    }
}