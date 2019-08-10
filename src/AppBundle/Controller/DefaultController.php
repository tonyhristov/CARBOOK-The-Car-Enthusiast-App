<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Post;
use AppBundle\Service\Posts\PostService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        if ($this->getUser()) {
            $post = $this
                ->getDoctrine()
                ->getRepository(Post::class)
                ->findBy([], ["addedOn" => "DESC"]);

            return $this->render('default/index.html.twig', ["posts" => $post]);
        } else {
            return $this->render('default/index.html.twig');
        }
    }


    /**
     * @Route("/about", name="aboutPage")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function aboutAction(Request $request)
    {
        return $this->render('about/about.html.twig');
    }


    /**
     * @Route("/contact", name="contactPage")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function contactAction(Request $request)
    {
        return $this->render('contact_me/contact.html.twig');
    }
}
