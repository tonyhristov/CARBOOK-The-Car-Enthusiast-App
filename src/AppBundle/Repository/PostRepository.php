<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping;
use Doctrine\ORM\OptimisticLockException;

/**
 * PostRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PostRepository extends \Doctrine\ORM\EntityRepository
{
//    public function __construct(EntityManagerInterface $em, Mapping\ClassMetadata $metadata = null)
//    {
//        parent::__construct($em,
//            $metadata == null ?
//                new Mapping\ClassMetadata(Post::class) :
//                $metadata);
//    }

    public function __construct(EntityManagerInterface $em, Mapping\ClassMetadata $metadata = null)
    {
        parent::__construct($em, $metadata == null ?
            new Mapping\ClassMetadata(Post::class) :
            $metadata);
    }

    public function save(Post $post)
    {
        try {
            $this->_em->persist($post);
            $this->_em->flush();
            return true;
        } catch (OptimisticLockException $e) {
            return false;
        }
    }

    public function edit(Post $post)
    {
        try {
            $this->_em->merge($post);
            $this->_em->flush();
            return true;
        } catch (OptimisticLockException $e) {
            return false;
        }
    }

    public function delete(Post $post)
    {
        try {
            $this->_em->remove($post);
            $this->_em->flush();
            return true;
        } catch (OptimisticLockException $e) {
            return false;
        }
    }
}