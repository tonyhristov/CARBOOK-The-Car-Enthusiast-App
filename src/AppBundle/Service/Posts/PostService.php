<?php


namespace AppBundle\Service\Posts;


use AppBundle\Entity\Post;
use AppBundle\Entity\User;
use AppBundle\Repository\PostRepository;
use AppBundle\Service\Users\UserService;

class PostService implements PostServiceInterface
{
    /**
     * @var PostRepository
     */
    private $postRepository;

    /**
     * @var UserService
     */
    private $userService;

    public function __construct(PostRepository $postRepository, UserService $userService)
    {
        $this->postRepository = $postRepository;
        $this->userService = $userService;
    }

    public function save(Post $post): bool
    {
        $author = $this->userService->currentUser();
        $post->setAuthor($author);
        return $this->postRepository->save($post);
    }

    public function edit(Post $post): bool
    {
        return $this->postRepository->edit($post);
    }

    public function delete(Post $post): bool
    {
        return $this->postRepository->delete($post);
    }

    public function getAll()
    {
        return $this->postRepository->findAll();
    }

    /**
     * @param int $id
     * @return Post|null|object
     */
    public function getOne(int $id): ?Post
    {
        return $this->postRepository->find($id);
    }
}
