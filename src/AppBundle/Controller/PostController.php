<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Post;
use AppBundle\Form\PostType;
use AppBundle\Service\Posts\PostService;
use AppBundle\Service\Users\UserService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends Controller
{
    /**
     * @var PostService
     */
    private $postService;


    /**
     * PostController constructor.
     * @param PostService $postService
     */
    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }


    /**
     * @Route("create_post", name="create_post", methods={"GET"})
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return Response
     */
    public function create()
    {
        return $this->render('posts/create_post.html.twig',
            [
                "form" => $this->createForm(PostType::class)->createView()
            ]);
    }

    /**
     * @Route("create_post", methods={"POST"})
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function createProcess(Request $request)
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        $this->uploadFile($form, $post);
        $this->postService->save($post);
        return $this->redirectToRoute("homepage");
    }

    /**
     * @Route("/post/{id}", name="post_view")
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function view($id)
    {
        $post = $this->postService->getOne($id);
        if ($post === null) {
            return $this->redirectToRoute("homepage");
        }

        return $this->render("posts/view_post.html.twig", ["post" => $post]);
    }

    /**
     * @Route("/posts/my_posts", name="my_posts")
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function getAllByUser()
    {
        $post = $this
            ->getDoctrine()
            ->getRepository(Post::class)
            ->findBy(["author" => $this->getUser()]);

        return $this->render("posts/my_posts.html.twig", ["posts" => $post]);
    }


    /**
     * @Route("/delete/{id}",name="post_delete")
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete(Request $request, int $id)
    {
        $post = $this
            ->postService
            ->getOne($id);

        if ($post === null) {
            return $this->redirectToRoute("homepage");
        }

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        $this->postService->delete($post);
        return $this->redirectToRoute("my_posts");
    }

    /**
     * @Route("/edit/{id}", name="edit_post", methods={"GET"})
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param int $id
     * @return Response
     */
    public function edit(int $id)
    {
        $post = $this->postService->getOne($id);

        if ($post === null) {
            return $this->redirectToRoute("homepage");
        }

        return $this->render("posts/edit_post.html.twig",
            [
                "form" => $this->createForm(PostType::class)->createView(),
                "post" => $post
            ]);

    }

    /**
     * @Route("/edit/{id}", methods={"POST"})
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function editProcess(Request $request, int $id)
    {
        $post = $this->postService->getOne($id);
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        $this->uploadFile($form, $post);
        $this->postService->edit($post);
        return $this->redirectToRoute("my_posts");
    }


    /**
     * @param FormInterface $form
     * @param Post $post
     */
    private function uploadFile(FormInterface $form, Post $post)
    {
        /**@var UploadedFile $file */
        $file = $form['image']->getData();
        if ($file === null) {
            $post->setImage(null);
        } else {
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();
            if ($file) {
                $file->move(
                    $this->getParameter("post_image_directory"),
                    $fileName
                );
                $post->setImage($fileName);
            }
        }
    }
}
