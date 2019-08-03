<?php


namespace AppBundle\Service\Users;


use AppBundle\Entity\User;

interface UserServiceInterface
{
    public function findOneByUsername(string $username): ?User;

    public function save(User $user): bool;

    public function findOneById(int $id): ?User;

    public function findOne(User $user): ?User;

    public function currentUser(): ?User;

    public function editProfile(User $user): bool;

    public function editPassword(User $user): bool;

}
