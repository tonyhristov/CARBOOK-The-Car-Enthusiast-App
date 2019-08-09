<?php


namespace AppBundle\Service\CurrentCars;


use AppBundle\Entity\CurrentCar;

interface CurrentCarsServiceInterface
{
    public function save(CurrentCar $currentCar): bool;

    public function delete(CurrentCar $currentCar): bool;

    public function edit(CurrentCar $currentCar): bool;

    public function getAllCurrentCars();

    public function getOne(int $id): ?CurrentCar;
}