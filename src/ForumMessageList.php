<?php

namespace Concrete\Package\OrticForum\Src;

use Concrete\Core\Search\ItemList\Database\AttributedItemList as DatabaseItemList;
use Concrete\Core\Search\Pagination\Pagination;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Pagerfanta\Adapter\DoctrineDbalAdapter;
use Package;

class ForumMessageList extends DatabaseItemList
{
    public function createQuery()
    {
        $this->query->select('m.mID')
            ->from('OrticForumMessages', 'm');
    }

    public function finalizeQuery(QueryBuilder $query)
    {
        return $query;
    }

    /**
     * Restrict list to topics and not their answers
     */
    public function filterByTopics()
    {
        $this->query->andWhere('m.parentMessageID is null');
    }

    /**
     * Filter by forum (page) ID
     *
     * @param $forumId
     */
    public function filterByForumId($forumId)
    {
        $this->query->andWhere('m.cID = :cID')->setParameter('cID', $forumId);
    }

    /**
     * Restricts the message list to answers of a certain topic defined by $parentMessageID
     *
     * @param $parentMessageID
     */
    public function filterByParent($parentMessageID)
    {
        $this->query->where('m.parentMessageID = :parentMessageId')->setParameter('parentMessageId', $parentMessageID);
    }

    /**
     * Filters messages by the forum (page) id they belong to. Specify the concrete5 internal collection/page id as an
     * array
     *
     * @param array $includedForumIds
     */
    public function filterByForumIds(array $includedForumIds)
    {
        $this->query->where('m.cID IN (:cIDs)')->setParameter('cIDs', $includedForumIds, Connection::PARAM_INT_ARRAY);
    }

    /**
     * The total results of the query.
     *
     * @return int
     */
    public function getTotalResults()
    {
        $query = $this->deliverQueryObject();

        return $query->resetQueryParts(['groupBy', 'orderBy'])->select('count(distinct m.mID)')->setMaxResults(1)->execute()->fetchColumn();
    }

    /**
     * Gets the pagination object for the query.
     *
     * @return Pagination
     */
    protected function createPaginationObject()
    {
        $adapter = new DoctrineDbalAdapter($this->deliverQueryObject(), function ($query) {
            $query->resetQueryParts(['groupBy', 'orderBy'])->select('count(distinct m.mID)')->setMaxResults(1);
        });
        $pagination = new Pagination($this, $adapter);

        return $pagination;
    }

    /**
     * @param $queryRow
     * @return mixed
     */
    public function getResult($queryRow)
    {
        $pkg = Package::getByHandle('ortic_forum');
        $em = $pkg->getEntityManager();
        $message = $em->getRepository('Concrete\Package\OrticForum\Src\Entity\ForumMessage')->find($queryRow['mID']);

        return $message;
    }

    /**
     * Not implemented since we aren't using attributes, but we still derive from DatabaseItemList as we get some
     * useful functionality for free.
     */
    protected function getAttributeKeyClassName()
    {
    }
}
