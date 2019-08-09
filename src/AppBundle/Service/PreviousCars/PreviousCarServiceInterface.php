<?php


namespace AppBundle\Service\PreviousCars;


use AppBundle\Entity\PreviousCar;
use AppBundle\Entity\User;

interface PreviousCarServiceInterface
{
    public function save(PreviousCar $previousCar): bool;

    public function delete(PreviousCar $previousCar): bool;

    public function edit(PreviousCar $previousCar): bool;

    public function getAllPreviousCars();

    public function getOne(int $id): ?PreviousCar;

}