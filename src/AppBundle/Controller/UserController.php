<?php

namespace AppBundle\Controller;

use AppBundle\Entity\CurrentCar;
use AppBundle\Entity\PreviousCar;
use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use AppBundle\Repository\PreviousCarRepository;
use AppBundle\Service\PreviousCars\PreviousCarService;
use AppBundle\Service\Users\UserServiceInterface;
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
     * @var UserServiceInterface
     */
    private $userService;

    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }


    /**
     * @Route("register", name="user_register", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function register(Request $request)
    {
        return $this->render('users/register.html.twig', [
            "form" => $this->createForm(UserType::class)->createView()
        ]);
    }

    /**
     * @Route("register",methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function registerProcess(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if (null !== $this->userService->findOneByUsername($form["username"]->getData())) {
            return $this->redirectToRoute("user_register");
        }
        $this->userService->save($user);
        return $this->redirectToRoute("security_login");
    }


    /**
     * @Route("/my_profile",  name="user_my_profile")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function profile()
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute("security_login");
        }
        return $this->render("users/my_profile.html.twig", ["user" => $this->userService->currentUser()]);
    }


    /**
     * @Route("edit_profile", name="user_edit_profile", methods={"GET"})
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return Response
     */
    public function edit(Request $request)
    {
        return $this->render('users/edit_my_profile.html.twig');
    }

    /**
     * @Route("edit_profile", methods={"POST"})
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return Response
     */
    public function editProcess(Request $request)
    {
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user);
        $form->remove("username");
        $form->remove("password");
        $form->handleRequest($request);

        $this->uploadFile($form, $user);
        $this->userService->editProfile($user);
        return $this->redirectToRoute("user_my_profile");
    }


    /**
     * @Route("edit_password", name="user_edit_password", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return Response
     */
    public function password(Request $request)
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute("security_login");
        }
        return $this->render('users/edit_my_password.html.twig');
    }

    /**
     * @Route("edit_password", methods={"POST"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return Response
     */
    public function passwordProcess(Request $request)
    {
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user);
        $form->remove('username');
        $form->remove('email');
        $form->remove('name');
        $form->remove('image');
        $form->handleRequest($request);

        $this->userService->editPassword($user);
        return $this->redirectToRoute("user_my_profile");
    }


    /**
     * @Route("edit_username", name="user_edit_username", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return Response
     */
    public function editUsername(Request $request)
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute("security_login");
        }
        return $this->render('users/edit_my_username.html.twig');
    }

    /**
     * @Route("edit_username", methods={"POST"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return Response
     */
    public function editUsernameProcess(Request $request)
    {
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user);
        $form->remove('password');
        $form->remove('email');
        $form->remove('name');
        $form->remove('image');
        $form->handleRequest($request);
        if (null !== $this->userService->findOneByUsername($form["username"]->getData())) {
            return $this->redirectToRoute("user_my_profile");
        }
        $this->userService->editProfile($user);
        return $this->redirectToRoute("user_my_profile");
    }


    /**
     * @Route("/{username}}",  name="user_profile")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewProfile(User $user)
    {
        $user = $this->userService->findOneByUsername($user->getUsername());
        $previousCar = $this
            ->getDoctrine()
            ->getRepository(PreviousCar::class)
            ->findBy(["driver" => $this->userService->findOne($user)]);
        $currentCar = $this
            ->getDoctrine()
            ->getRepository(CurrentCar::class)
            ->findBy(["driver" => $this->userService->findOne($user)]);
        if (!$this->getUser()) {
            return $this->redirectToRoute("security_login");
        }
        return $this->render("users/view_profile.html.twig", [
            "user" => $user,
            "previousCars" => $previousCar,
            "currentCars" => $currentCar
        ]);
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
        /**@var UploadedFile $file */
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
        }
    }
}
