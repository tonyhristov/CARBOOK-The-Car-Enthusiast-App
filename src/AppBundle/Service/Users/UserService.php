<?php


namespace AppBundle\Service\Users;


use AppBundle\Entity\User;
use AppBundle\Repository\UserRepository;
use AppBundle\Service\Encryption\BCryptService;
use AppBundle\Service\Encryption\EncryptionServiceInterface;
use Symfony\Component\Security\Core\Security;

class UserService implements UserServiceInterface
{

    private $security;
    private $userRepository;
    private $encryptionService;

    public function __construct(Security $security, UserRepository $userRepository, BCryptService $encryptionService)
    {
        $this->security = $security;
        $this->userRepository = $userRepository;
        $this->encryptionService = $encryptionService;
    }

    /**
     * @param string $username
     * @return User|null|object
     */
    public function findOneByUsername(string $username): ?User
    {
        return $this->userRepository->findOneBy(["username" => $username]);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function save(User $user): bool
    {
        $passwordHash = $this->encryptionService->hash($user->getPassword());
        $user->setPassword($passwordHash);
        return $this->userRepository->insert($user);
    }

    /**
     * @param int $id
     * @return User|null|object
     */
    public function findOneById(int $id): ?User
    {
        return $this->userRepository->find($id);

    }

    /**
     * @param User $user
     * @return User|null|object
     */
    public function findOne(User $user): ?User
    {
        return $this->userRepository->find($user);
    }

    /**
     * @return User|null|object
     */
    public function currentUser(): ?User
    {
        return $this->security->getUser();
    }

    public function editProfile(User $user): bool
    {
        return $this->userRepository->edit($user);
    }

    public function editPassword(User $user): bool
    {
        $passwordHash = $this->encryptionService->hash($user->getPassword());
        $user->setPassword($passwordHash);
        return $this->userRepository->edit($user);
    }
}
