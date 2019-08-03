<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends Controller
{
    /**
     * @Route("register", name="user_register")
     * @param Request $request
     * @return Response
     */
    public function register(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $passwordHash = $this->get("security.password_encoder")
                ->encodePassword($user, $user->getPassword());


            $user->setPassword($passwordHash);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute("security_login");
        }
        return $this->render('users/register.html.twig');
    }

    /**
     * @Route("/my_profile",  name="user_my_profile")
     */
    public function profile()
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute("security_login");
        }

        $userRepository = $this
            ->getDoctrine()
            ->getRepository(User::class);
        $currentUser = $userRepository->find($this->getUser());

        return $this->render("users/my_profile.html.twig", ["user" => $currentUser]);
    }


    /**
     * @Route("edit_profile", name="user_edit_profile")
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return Response
     */
    public function edit(Request $request)
    {
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user);
        $form->remove("password");
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            $this->uploadFile($form, $user);

            $em = $this->getDoctrine()->getManager();
            $em->merge($user);
            $em->flush();

            return $this->redirectToRoute("user_my_profile");
        }
        return $this->render('users/edit_my_profile.html.twig');
    }

    /**
     * @Route("edit_password", name="user_edit_password")
     * @param Request $request
     * @return Response
     */
    public function password(Request $request)
    {
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user);
        $form->remove('username');
        $form->remove('email');
        $form->remove('name');
        $form->remove('image');
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $passwordHash = $this
                ->get("security.password_encoder")
                ->encodePassword($user, $user->getPassword());

            $user->setPassword($passwordHash);
            $em = $this->getDoctrine()->getManager();
            $em->merge($user);
            $em->flush();

            return $this->redirectToRoute("user_my_profile");
        }
        return $this->render('users/edit_my_password.html.twig');
    }


    /**
     * @Route("/logout", name="security_logout")
     * @throws Exception
     */
    public function logout()
    {
        throw new Exception("Logout failed");
    }

    /**
     * @param \Symfony\Component\Form\FormInterface $form
     * @param $user
     */
    private function uploadFile(FormInterface $form, User $user)
    {
        /**
         * @var UploadedFile $file
         */
        $file = $form['image']->getData();
        if ($file === null) {
            $user->setImage(null);
        } else {
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();


            if ($file) {
                $file->move(
                    $this->getParameter("profile_image_directory"),
                    $fileName
                );
                $user->setImage($fileName);
            }

//        $fs = new filesystem();
////        $file = $this->getParameter("profile_image_directory" . '/' . $user->getImage());
////        $fs->remove(array($file));

        }
    }
}
