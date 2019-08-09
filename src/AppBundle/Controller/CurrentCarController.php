<?php

namespace AppBundle\Controller;

use AppBundle\Entity\CurrentCar;
use AppBundle\Form\CurrentCarType;
use AppBundle\Service\CurrentCars\CurrentCarsService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CurrentCarController extends Controller
{
    /**
     * @var CurrentCarsService
     */
    private $currentCarsService;

    public function __construct(CurrentCarsService $currentCarsService)
    {
        $this->currentCarsService = $currentCarsService;
    }


    /**
     * @Route("/my_current_cars", name="current_cars")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getAllCurrentCarsByDriver()
    {
        $currentCar = $this->currentCarsService->getAllCurrentCars();
        return $this->render('current_car/all_current_cars.htm.twig', ["currentCars" => $currentCar]);
    }

    /**
     * @Route("create_current_car", name="create_current_car", methods={"GET"})
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return Response
     */
    public function create()
    {
        return $this->render("current_car/create_current_cars.htm.twig",
            [
                "form" => $this->createForm(CurrentCarType::class)->createView()
            ]
        );
    }

    /**
     * @Route("create_current_car", methods={"POST"})
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function createProcess(Request $request)
    {
        $currentCar = new CurrentCar();
        $form = $this->createForm(CurrentCarType::class, $currentCar);
        $form->handleRequest($request);

        $this->uploadFile($form, $currentCar);
        $this->currentCarsService->save($currentCar);
        return $this->redirectToRoute("current_cars");
    }

    /**
     * @Route("/delete_current_car/{id}",name="current_car_delete")
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete(Request $request, int $id)
    {
        $currentCar = $this->currentCarsService->getOne($id);
        if ($currentCar === null) {
            return $this->redirectToRoute("current_cars");
        }
        $form = $this->createForm(CurrentCarType::class, $currentCar);
        $form->handleRequest($request);
        $this->currentCarsService->delete($currentCar);
        return $this->redirectToRoute("current_cars");
    }

    /**
     * @Route("/edit_current_car/{id}", name="edit_current_car", methods={"GET"})
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param int $id
     * @return Response
     */
    public function edit(int $id)
    {
        $currentCar = $this->currentCarsService->getOne($id);
        if ($currentCar === null) {
            return $this->redirectToRoute("current_cars");
        }
        return $this->render("current_car/edit_current_cars.htm.twig",
            [
                "form" => $this->createForm(CurrentCarType::class)->createView(),
                "currentCar" => $currentCar
            ]);
    }

    /**
     * @Route("/edit_current_car/{id}", methods={"POST"})
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function editProcess(Request $request, int $id)
    {
        $currentCar = $this->currentCarsService->getOne($id);
        $form = $this->createForm(CurrentCarType::class, $currentCar);
        $form->handleRequest($request);
        $this->uploadFile($form, $currentCar);
        $this->currentCarsService->edit($currentCar);
        return $this->redirectToRoute("current_cars");
    }


    /**
     * @param FormInterface $form
     * @param CurrentCar $currentCar
     */
    private function uploadFile(FormInterface $form, CurrentCar $currentCar)
    {
        /**@var UploadedFile $file */
        $file = $form['image']->getData();
        if ($file === null) {
            $currentCar->setImage(null);
        } else {
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();
            if ($file) {
                $file->move(
                    $this->getParameter("current_car_image_directory"),
                    $fileName
                );
                $currentCar->setImage($fileName);
            }
        }
    }
}
