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
        $token = Core::make('token');

        if ($this->getRequest()->isPost()) {
            if ($token->validate('writeAnswer')) {
                $currentPage = Page::getCurrentPage();

                $forum = Core::make('ortic/forum');
                $forum->writeAnswer($currentPage, $this->post('message'));

                $this->flash('forumSuccess', t('Message added'));
                return Redirect::to($this->action(''));

            } else {
                $this->flash('forumError', $token->getErrorMessage());
                return Redirect::to($this->action(''));
            }
        }

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
        $token = Core::make('token');

        if ($this->getRequest()->isPost()) {
            if ($token->validate('updateMessage')) {
                $forum = Core::make('ortic/forum');
                $message = $forum->getMessage($messageId);

                if (!$forum->canEditMessage($message)) {
                    header("HTTP/1.0 403 Forbidden");
                    $this->replace('/page_forbidden');
                    return;
                }

                $forum->updateMessage($message, $this->post('message'));

                $this->flash('forumSuccess', t('Message updated.'));
                return Redirect::to($this->action(''));

            } else {
                $this->flash('forumError', $token->getErrorMessage());
                return Redirect::to($this->action(''));
            }
        }

        return Redirect::to($this->action(''));
    }

    /**
     * Deletes an existing message
     *
     * @param int $messageId
     * @param string $tokenId
     * @return \Concrete\Core\Routing\RedirectResponse|void
     */
    public function deleteMessage(int $messageId, $tokenId)
    {
        $token = Core::make('token');

        if ($token->validate('deleteMessage', $tokenId)) {
            $forum = Core::make('ortic/forum');
            $message = $forum->getMessage($messageId);

            if (!$forum->canEditMessage($message)) {
                header("HTTP/1.0 403 Forbidden");
                $this->replace('/page_forbidden');
                return;
            }

            $forum->deleteMessage($message);

            $this->flash('forumSuccess', t('Message deleted.'));
            return Redirect::to($this->action(''));

        } else {
            $this->flash('forumError', $token->getErrorMessage());
            return Redirect::to($this->action(''));
        }

        return Redirect::to($this->action(''));
    }

}