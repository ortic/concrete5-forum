<?php

namespace Concrete\Package\OrticForum\Controller\PageType;

use Concrete\Core\Page\Controller\PageTypeController;
use Concrete\Core\Routing\Redirect;
use Core;
use Page;
use User;

class ForumTopic extends PageTypeController
{

    public function view()
    {
        $this->requireAsset('ortic/forum');

        $currentPage = Page::getCurrentPage();
        $forum = Core::make('ortic/forum');

        $messages = $forum->getMessages($currentPage);

        $this->set('messages', $messages);
        $this->set('user', new User());
        $this->set('currentPage', $currentPage);

        $this->render('topic', 'ortic_forum');
    }

    /**
     * Adds a message to an existing topic
     *
     * @return \Concrete\Core\Routing\RedirectResponse
     */
    public function writeAnswer()
    {
        $currentPage = Page::getCurrentPage();

        $forum = Core::make('ortic/forum');
        $forum->writeAnswer($currentPage, $this->post('message'));

        $this->flash('message', t('Message added'));
        return Redirect::to($this->action(''));
    }

    /**
     * Updates an existing message
     *
     * @param int $messageId
     * @return \Concrete\Core\Routing\RedirectResponse|void
     */
    public function updateMessage(int $messageId)
    {
        $forum = Core::make('ortic/forum');
        $message = $forum->getMessage($messageId);
        $user = new User();

        if ($user->getUserId() != $message->user->getUserId()) {
            header("HTTP/1.0 403 Forbidden");
            $this->replace('/page_forbidden');
            return;
        }

        $forum->updateMessage($message, $this->post('message'));

        $this->flash('message', t('Message updated'));
        return Redirect::to($this->action(''));
    }

}