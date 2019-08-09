<?php


namespace AppBundle\Service\PreviousCars;


use AppBundle\Entity\PreviousCar;
use AppBundle\Entity\User;
use AppBundle\Repository\PreviousCarRepository;
use AppBundle\Service\Users\UserService;

class PreviousCarService implements PreviousCarServiceInterface
{
    /**
     * @var PreviousCarRepository
     */
    private $previousCarRepository;

    /**
     * @var UserService
     */
    private $userService;

    public function __construct(PreviousCarRepository $previousCarRepository, UserService $userService)
    {
        $this->previousCarRepository = $previousCarRepository;
        $this->userService = $userService;
    }

    public function save(PreviousCar $previousCar): bool
    {
        $driver = $this->userService->currentUser();
        $previousCar->setDriver($driver);
        return $this->previousCarRepository->save($previousCar);
    }

    public function delete(PreviousCar $previousCar): bool
    {
        return $this->previousCarRepository->delete($previousCar);
    }

    public function edit(PreviousCar $previousCar): bool
    {
        return $this->previousCarRepository->edit($previousCar);
    }

    public function getAllPreviousCars()
    {
        return $this->previousCarRepository->findBy(["driver" => $this->userService->currentUser()]);
    }

    /**
     * @param int $id
     * @return PreviousCar|null|object
     */
    public function getOne(int $id): ?PreviousCar
    {
        return $this->previousCarRepository->find($id);
    }

}