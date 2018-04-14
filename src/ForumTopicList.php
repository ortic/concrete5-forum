<?php

namespace Concrete\Package\OrticForum\Src;

use Concrete\Core\Page\PageList;
use Doctrine\DBAL\Query\QueryBuilder;


class ForumTopicList extends PageList
{

    public function finalizeQuery(QueryBuilder $query)
    {
        $finalQuery = parent::finalizeQuery($query);

        $finalQuery
            ->leftJoin('p', 'OrticForumMessages', 'ofm', 'p.cID = ofm.cID')
            ->andWhere('ofm.mID is null or ofm.lastMessage=1')
            ->addSelect('ofm.dateCreated lastMessageCreated');

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

        $page->lastMessageCreated = $queryRow['lastMessageCreated'];

        return $page;
    }

    public function sortByLastActivityDate(string $dir = 'asc')
    {
        $this->query->orderBy('if(ofm.mID is null, cv.cvDateCreated, ofm.dateCreated)', $dir);
    }

}