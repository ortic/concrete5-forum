<?php

namespace Concrete\Package\OrticForum\Src\Entity;

use Concrete\Core\Entity\User\User;
use Concrete\Core\File\Tracker\FileTrackableInterface;
use Concrete\Core\Page\Collection\Collection;
use Core;
use File;
use Page;

/**
 * @Entity
 * @Table(name="OrticForumMessages")
 */
class ForumMessage implements FileTrackableInterface
{
    /**
     * @Id
     * @Column(name="mID", type="integer", options={"unsigned"=true})
     * @GeneratedValue(strategy="AUTO")
     */
    protected $ID;

    /**
     * @Column(name="cID", type="integer", options={"unsigned"=true})
     */
    protected $pageId;

    /**
     * @ManyToOne(targetEntity="\Concrete\Core\Entity\User\User")
     * @JoinColumn(name="userId", referencedColumnName="uID", onDelete="SET NULL")
     **/
    public $user;

    /**
     * @Column(type="text")
     */
    protected $message;

    /**
     * @Column(type="boolean")
     */
    protected $lastMessage;

    /**
     * @Column(type="boolean")
     */
    protected $firstMessage;

    /**
     * @Column(type="datetime")
     */
    protected $dateCreated;

    /**
     * @Column(type="datetime")
     */
    protected $dateUpdated;

    /**
     * @Column(type="integer", nullable=true)
     */
    protected $attachmentFileId;

    /**
     * @return mixed
     */
    public function getAttachmentFileId()
    {
        return $this->attachmentFileId;
    }

    /**
     * @param mixed $attachmentFileId
     * @return ForumMessage
     */
    public function setAttachmentFileId($attachmentFileId)
    {
        $this->attachmentFileId = $attachmentFileId;
        return $this;
    }

    /**
     * @return \Concrete\Core\Entity\File\File|null
     */
    public function getAttachmentFile()
    {
        if ($this->attachmentFileId) {
            $f = File::getByID($this->attachmentFileId);

            return $f;
        }
    }

    /**
     * @return mixed
     */
    public function getID()
    {
        return $this->ID;
    }

    /**
     * @param mixed $ID
     * @return ForumMessage
     */
    public function setID($ID)
    {
        $this->ID = $ID;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLastMessage()
    {
        return $this->lastMessage;
    }

    /**
     * @param mixed $lastMessage
     * @return ForumMessage
     */
    public function setLastMessage($lastMessage)
    {
        $this->lastMessage = $lastMessage;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFirstMessage()
    {
        return $this->firstMessage;
    }

    /**
     * @param mixed $firstMessage
     * @return ForumMessage
     */
    public function setFirstMessage($firstMessage)
    {
        $this->firstMessage = $firstMessage;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPageId()
    {
        return $this->pageId;
    }

    /**
     * @param mixed $pageId
     * @return ForumMessage
     */
    public function setPageId($pageId)
    {
        $this->pageId = $pageId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param mixed $userId
     * @return ForumMessage
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     * @return ForumMessage
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * @param mixed $dateCreated
     * @return ForumMessage
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDateUpdated()
    {
        return $this->dateUpdated;
    }

    /**
     * @param mixed $dateUpdated
     * @return ForumMessage
     */
    public function setDateUpdated($dateUpdated)
    {
        $this->dateUpdated = $dateUpdated;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return $this
     */
    public function setUser(User $user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Returns a direct link to the message
     */
    public function getLink()
    {
        $forum = Core::make('ortic/forum');
        return $forum->getLink($this);
    }

    public function canEdit()
    {
        $forum = Core::make('ortic/forum');
        return $forum->canEditMessage($this);
    }

    /**
     * @return Collection The collection these files are attached to
     */
    public function getUsedCollection()
    {
        return Page::getByID($this->getPageId());
    }

    /**
     * @return array An array of file IDs or file objects
     */
    public function getUsedFiles()
    {
        if ($attachmentFileId = $this->getAttachmentFileId()) {
            return [$attachmentFileId];
        }
    }
}