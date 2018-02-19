<?php

namespace Concrete\Package\OrticForum\Block\ForumNews;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Page\PageList;
use Concrete\Package\OrticForum\Src\ForumList;
use Concrete\Package\OrticForum\Src\ForumMessageList;

class Controller extends BlockController
{
    protected $btTable = 'btOrticForumNews';

    protected $btInterfaceWidth = "450";
    protected $btInterfaceHeight = "400";
    protected $btCacheBlockRecord = false;
    protected $btCacheBlockOutput = false;
    protected $btCacheBlockOutputOnPost = false;
    protected $btCacheBlockOutputForRegisteredUsers = false;

    public function getBlockTypeName()
    {
        return t('Forum News');
    }

    public function getBlockTypeDescription()
    {
        return t('Shows the latest messages from the forum');
    }

    protected function getIncludedForumIds()
    {
        return preg_split('/,/', $this->forumsToInclude);
    }

    public function form()
    {
        $forumList = new PageList();
        $forumList->filterByPageTypeHandle('forum');
        $forums = $forumList->getResults();

        $forumsToInclude = $this->getIncludedForumIds();

        $this->set('forums', $forums);
        $this->set('forumsToInclude', $forumsToInclude);
    }

    public function add()
    {
        $this->set('tickAllForums', true);
        $this->set('messagesToShow', 5);

        $this->form();
    }

    public function edit()
    {
        $this->set('tickAllForums', false);

        $this->form();
    }

    public function view()
    {
        $forumMessageList = new ForumMessageList();
        $forumMessageList->filterByForumIds($this->getIncludedForumIds());
        $forumMessageList->setItemsPerPage($this->messagesToShow ?: 5);
        $forumMessageList->sortBy('dateCreated', 'desc');

        $forumMessages = $forumMessageList->getResults();

        $this->set('forumMessages', $forumMessages);
    }

    public function save($args)
    {
        $args['forumsToInclude'] = isset($args['forumsToInclude']) ? join(',', $args['forumsToInclude']) : '';
        parent::save($args);
    }
}