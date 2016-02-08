<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * TagRepository
 *
 * This class was generated by the PhpStorm "Php Annotations" Plugin. Add your own custom
 * repository methods below.
 */
class TagRepository extends EntityRepository
{
    public function findAllTagsWithDependencies()
    {
        return $this->createQueryBuilder('t')
            ->select('t, p')
            ->leftJoin('t.posts', 'p')
            ->getQuery()
            ->getResult();
    }

    public function findTagWithPosts($slug)
    {
        return $this->createQueryBuilder('t')
            ->select('t, p, c')
            ->leftJoin('t.posts', 'p')
            ->leftJoin('p.comments', 'c')
            ->where('t.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();

    }

    public function findAllSorted()
    {
        return $this->createQueryBuilder('t')
            ->select('t')
            ->orderBy('t.name')
            ->getQuery()
            ->getResult();
    }

    public function findAllTags($currentPage, $limit)
    {
        $query = $this->createQueryBuilder('t')
            ->select('t')
            ->orderBy('t.name', 'ASC')
            ->getQuery()
            ->setFirstResult($limit * ($currentPage - 1))
            ->setMaxResults($limit);

        return new Paginator($query);

    }

    public function countAllTags()
    {
        return $this->createQueryBuilder('t')
            ->select('count(t.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
