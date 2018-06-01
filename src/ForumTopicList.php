<?php

namespace Concrete\Package\OrticForum\Src;

use Concrete\Core\Page\PageList;
use Doctrine\DBAL\Query\QueryBuilder;

class ForumTopicList extends PageList
{

    /**
     * join our forum message table to get the time of the last activity
     * @param QueryBuilder $query
     * @return QueryBuilder
     */
    public function finalizeQuery(QueryBuilder $query)
    {
        $finalQuery = parent::finalizeQuery($query);

        $finalQuery
            ->innerJoin('p', 'OrticForumMessages', 'ofm', 'p.cID = ofm.cID')
            ->andWhere('ofm.lastMessage=1')
            ->addSelect('ofm.mID lastMessageId, ofm.dateCreated lastMessageCreated, ofm.userId lastMessageCreatedByUserId, ofm.views, (select count(*) from OrticForumMessages ofm where ofm.cID=p.cID) messageCount');

        return $finalQuery;
    }

    /**
     * @param $queryRow
     *
     * @return \Concrete\Core\Page\Page
     */
    public function getResult($queryRow)
    {
        $page = parent::getResult($queryRow);

        $page->lastMessageId = $queryRow['lastMessageId'];
        $page->lastMessageCreated = $queryRow['lastMessageCreated'];
        $page->lastMessageCreatedByUserId = $queryRow['lastMessageCreatedByUserId'];
        $page->messageCount = $queryRow['messageCount'];
        $page->views = $queryRow['views'];

        return $page;
    }

    public function sortByLastActivityDate(string $dir = 'asc')
    {
        $this->query->orderBy('if(ofm.mID is null, cv.cvDateCreated, ofm.dateCreated)', $dir);
    }

}