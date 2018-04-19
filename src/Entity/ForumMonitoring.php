<?php

namespace Concrete\Package\OrticForum\Src\Entity;

/**
 * @Entity
 * @Table(name="OrticForumMonitoring")
 */
class ForumMonitoring
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
     * @return mixed
     */
    public function getID()
    {
        return $this->ID;
    }

    /**
     * @param mixed $ID
     * @return ForumMonitoring
     */
    public function setID($ID)
    {
        $this->ID = $ID;
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
     * @return ForumMonitoring
     */
    public function setPageId($pageId)
    {
        $this->pageId = $pageId;
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
     * @param mixed $user
     * @return ForumMonitoring
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

}