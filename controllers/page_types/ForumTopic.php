<?php

namespace Concrete\Package\OrticForum\Controller\PageType;

use Concrete\Core\Page\Controller\PageTypeController;
use Concrete\Core\Routing\Redirect;
use Concrete\Package\OrticForum\Src\AuthenticationTrait;
use Core;
use Page;
use User;

class ForumTopic extends PageTypeController
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

    public function view()
    {
        $this->requireAsset('ortic/forum');

        $currentPage = Page::getCurrentPage();
        $forum = Core::make('ortic/forum');

        $messages = $forum->getMessages($currentPage);

        $this->set('messages', $messages);
        $this->set('user', new User());
        $this->set('isMonitoring', $forum->isMonitoring($currentPage));
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
        $forum = Core::make('ortic/forum');

        if ($this->getRequest()->isPost()) {
            if (!$token->validate('writeAnswer')) {
                $this->error->add($token->getErrorMessage());
            }

            if (!$this->get('message')) {
                $this->error->add(t('You must enter a message'));
            }

            // upload attachment, but only if there are no errors, otherwise we'd upload the file without having any
            // association to it
            $attachment = null;
            if (!$this->error->has()) {
                try {
                    $attachment = $forum->uploadAttachment($_FILES['attachment']);
                }
                catch (\Exception $ex) {
                    $this->error->add($ex->getMessage());
                }
            }

            if (!$this->error->has()) {
                $forumMessage = $forum->writeAnswer($this->post('message'), $attachment);

                // sign up user to receive notifications
                $topicPage = Page::getByID($forumMessage->getPageId());
                if ($this->post('subscribe')) {
                    $forum->subscribeForTopicChanges($topicPage);
                }
                else {
                    $forum->unsubscribeFromTopicChanges($topicPage);
                }

                $this->flash('forumSuccess', t('Message added'));
                return Redirect::to($this->action(''));

            } else {
                $this->flash('forumError', $this->error);
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

            // check if we have deleted the last message, the topic, if we did, we remove the page as well
            $currentPage = Page::getCurrentPage();
            $messages = $forum->getMessages($currentPage);
            if (empty($messages)) {
                $currentPage->delete();

                return Redirect::to($this->action('..'));
            }
            else {
                return Redirect::to($this->action(''));
            }

        } else {
            $this->flash('forumError', $token->getErrorMessage());
            return Redirect::to($this->action(''));
        }
    }

    /**
     * Stops the current user from receiving notifications on new answers to the current topic
     * 
     * @return \Concrete\Core\Routing\RedirectResponse
     */
    public function stopMonitoring()
    {
        $forum = Core::make('ortic/forum');
        $forum->unsubscribeFromTopicChanges(Page::getCurrentPage());

        $this->flash('forumSuccess', t('Monitoring disabled.'));
        return Redirect::to($this->action(''));
    }


    /**
     * Subscribers the current user to changes to the current topic
     *
     * @return \Concrete\Core\Routing\RedirectResponse
     */
    public function startMonitoring()
    {
        $forum = Core::make('ortic/forum');
        $forum->subscribeForTopicChanges(Page::getCurrentPage());

        $this->flash('forumSuccess', t('Monitoring enabled.'));
        return Redirect::to($this->action(''));
    }

}