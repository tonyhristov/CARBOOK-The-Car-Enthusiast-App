<?php

namespace AppBundle\Controller;

use AppBundle\Entity\PreviousCar;
use AppBundle\Form\PreviousCarType;
use AppBundle\Service\PreviousCars\PreviousCarService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PreviousCarController extends Controller
{
    /**
     * @var PreviousCarService
     */
    private $previousCarService;

    public function __construct(PreviousCarService $previousCarService)
    {
        $this->previousCarService = $previousCarService;
    }


    /**
     * @Route("/my_previous_cars", name="previous_cars")
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getAllCarsByUser()
    {
        $previousCar = $this->previousCarService->getAllPreviousCars();
        return $this->render("previous_car/previous_cars.html.twig", ["previousCars" => $previousCar]);
    }


    /**
     * @Route("create_previous_car", name="create_previous_car", methods={"GET"})
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return Response
     */
    public function create()
    {
        return $this->render('previous_car/create.html.twig',
            [
                "form" => $this->createForm(PreviousCarType::class)->createView()
            ]);
    }

    /**
     * @Route("create_previous_car", methods={"POST"})
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function createProcess(Request $request)
    {
        $previousCar = new PreviousCar();
        $form = $this->createForm(PreviousCarType::class, $previousCar);
        $form->handleRequest($request);
        $this->uploadFile($form, $previousCar);
        $this->previousCarService->save($previousCar);
        return $this->redirectToRoute("previous_cars");
    }


    /**
     * @Route("/delete_previous_car/{id}",name="previous_car_delete")
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete(Request $request, int $id)
    {
        $previousCar = $this
            ->previousCarService
            ->getOne($id);
        if ($previousCar === null) {
            return $this->redirectToRoute("previous_cars");
        }
        $form = $this->createForm(PreviousCarType::class, $previousCar);
        $form->handleRequest($request);
        $this->previousCarService->delete($previousCar);
        return $this->redirectToRoute("previous_cars");
    }


    /**
     * @Route("/edit_previous_car/{id}", name="edit_previous_car", methods={"GET"})
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param int $id
     * @return Response
     */
    public function edit(int $id)
    {
        $previousCar = $this->previousCarService->getOne($id);
        if ($previousCar === null) {
            return $this->redirectToRoute("homepage");
        }
        return $this->render("previous_car/edit.html.twig",
            [
                "form" => $this->createForm(PreviousCarType::class)->createView(),
                "previousCar" => $previousCar
            ]);
    }

    /**
     * @Route("/edit_previous_car/{id}", methods={"POST"})
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function editProcess(Request $request, int $id)
    {
        $post = $this->previousCarService->getOne($id);
        $form = $this->createForm(PreviousCarType::class, $post);
        $form->handleRequest($request);
        $this->uploadFile($form, $post);
        $this->previousCarService->edit($post);
        return $this->redirectToRoute("previous_cars");
    }


    /**
     * @param FormInterface $form
     * @param PreviousCar $previousCar
     */
    private function uploadFile(FormInterface $form, PreviousCar $previousCar)
    {
        /**@var UploadedFile $file */
        $file = $form['image']->getData();
        if ($file === null) {
            $previousCar->setImage(null);
        } else {
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();
            if ($file) {
                $file->move(
                    $this->getParameter("previous_car_image_directory"),
                    $fileName
                );
                $previousCar->setImage($fileName);
            }
        }
    }
}
