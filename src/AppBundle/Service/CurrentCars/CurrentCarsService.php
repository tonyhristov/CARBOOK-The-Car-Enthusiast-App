<?php


namespace AppBundle\Service\CurrentCars;


use AppBundle\Entity\CurrentCar;
use AppBundle\Repository\CurrentCarRepository;
use AppBundle\Service\Users\UserService;

class CurrentCarsService implements CurrentCarsServiceInterface
{
    /**
     * @var CurrentCarRepository
     */
    private $currentCarRepository;

    /**
     * @var UserService
     */
    private $userService;

    public function __construct(CurrentCarRepository $currentCarRepository, UserService $userService)
    {
        $this->currentCarRepository = $currentCarRepository;
        $this->userService = $userService;
    }

    public function save(CurrentCar $currentCar): bool
    {
        $user = $this->userService->currentUser();
        $currentCar->setDriver($user);
        return $this->currentCarRepository->save($currentCar);
    }

    public function delete(CurrentCar $currentCar): bool
    {
        return $this->currentCarRepository->delete($currentCar);
    }

    public function edit(CurrentCar $currentCar): bool
    {
        return $this->currentCarRepository->edit($currentCar);
    }

    public function getAllCurrentCars()
    {
        return $this->currentCarRepository->findBy(["driver" => $this->userService->currentUser()]);
    }

    /**
     * @param int $id
     * @return CurrentCar|null|object
     */
    public function getOne(int $id): ?CurrentCar
    {
        return $this->currentCarRepository->find($id);
    }
}