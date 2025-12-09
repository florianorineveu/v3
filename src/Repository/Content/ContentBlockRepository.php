<?php

declare(strict_types=1);

namespace App\Repository\Content;

use App\Entity\Blog\Post;
use App\Entity\Content\ContentBlock;
use App\Entity\Portfolio\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ContentBlock>
 */
class ContentBlockRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContentBlock::class);
    }

    /**
     * Find all blocks for a Post with eager loading of all relations.
     * Single SQL query with LEFT JOINs for optimal performance.
     *
     * @return ContentBlock[]
     */
    public function findByPostWithRelations(Post $post): array
    {
        return $this->createQueryBuilder('cb')
            ->leftJoin('cb.references', 'r')->addSelect('r')
            ->leftJoin('r.post', 'p')->addSelect('p')
            ->leftJoin('p.category', 'pc')->addSelect('pc')
            ->leftJoin('r.project', 'pr')->addSelect('pr')
            ->where('cb.post = :post')
            ->setParameter('post', $post)
            ->orderBy('cb.position', 'ASC')
            ->addOrderBy('r.position', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find all blocks for a Project with eager loading of all relations.
     * Single SQL query with LEFT JOINs for optimal performance.
     *
     * @return ContentBlock[]
     */
    public function findByProjectWithRelations(Project $project): array
    {
        return $this->createQueryBuilder('cb')
            ->leftJoin('cb.references', 'r')->addSelect('r')
            ->leftJoin('r.post', 'p')->addSelect('p')
            ->leftJoin('p.category', 'pc')->addSelect('pc')
            ->leftJoin('r.project', 'pr')->addSelect('pr')
            ->where('cb.project = :project')
            ->setParameter('project', $project)
            ->orderBy('cb.position', 'ASC')
            ->addOrderBy('r.position', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get the maximum position for blocks of a Post.
     *
     * @return int Returns -1 if no blocks exist
     */
    public function getMaxPositionForPost(Post $post): int
    {
        $result = $this->createQueryBuilder('cb')
            ->select('MAX(cb.position)')
            ->where('cb.post = :post')
            ->setParameter('post', $post)
            ->getQuery()
            ->getSingleScalarResult();

        return (int) ($result ?? -1);
    }

    /**
     * Get the maximum position for blocks of a Project.
     *
     * @return int Returns -1 if no blocks exist
     */
    public function getMaxPositionForProject(Project $project): int
    {
        $result = $this->createQueryBuilder('cb')
            ->select('MAX(cb.position)')
            ->where('cb.project = :project')
            ->setParameter('project', $project)
            ->getQuery()
            ->getSingleScalarResult();

        return (int) ($result ?? -1);
    }

    /**
     * Find blocks by type for a Post.
     *
     * @return ContentBlock[]
     */
    public function findByPostAndType(Post $post, string $type): array
    {
        return $this->createQueryBuilder('cb')
            ->leftJoin('cb.references', 'r')->addSelect('r')
            ->leftJoin('r.post', 'p')->addSelect('p')
            ->leftJoin('r.project', 'pr')->addSelect('pr')
            ->where('cb.post = :post')
            ->andWhere('cb.type = :type')
            ->setParameter('post', $post)
            ->setParameter('type', $type)
            ->orderBy('cb.position', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find blocks by type for a Project.
     *
     * @return ContentBlock[]
     */
    public function findByProjectAndType(Project $project, string $type): array
    {
        return $this->createQueryBuilder('cb')
            ->leftJoin('cb.references', 'r')->addSelect('r')
            ->leftJoin('r.post', 'p')->addSelect('p')
            ->leftJoin('r.project', 'pr')->addSelect('pr')
            ->where('cb.project = :project')
            ->andWhere('cb.type = :type')
            ->setParameter('project', $project)
            ->setParameter('type', $type)
            ->orderBy('cb.position', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
