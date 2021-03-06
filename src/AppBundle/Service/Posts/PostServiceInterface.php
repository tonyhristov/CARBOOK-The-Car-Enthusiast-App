<?php


namespace AppBundle\Service\Posts;


use AppBundle\Entity\Post;
use AppBundle\Entity\User;

interface PostServiceInterface
{
    public function save(Post $post): bool;

    public function edit(Post $post): bool;

    public function delete(Post $post): bool;

    public function getAll();

    public function getOne(int $id): ?Post;

}