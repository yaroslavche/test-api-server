<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Repository\GroupRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Service\Attribute\Required;

final class UserService implements UserServiceInterface
{
    public function __construct(
        #[Required] public GroupRepository $groupRepository,
        #[Required] public UserRepository $userRepository,
        #[Required] public EntityManagerInterface $entityManager,
    ) {
    }

    public function create(string $email, string $name): void
    {
        if (false === filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('User email must be valid email', 400);
        }
        if ('' === $name) {
            throw new \InvalidArgumentException('User name must be not empty', 400);
        }
        $existsEmail = $this->userRepository->findOneBy(['email' => $email]);
        if (null !== $existsEmail) {
            throw new \InvalidArgumentException(sprintf('User with email "%s" already exists', $email), 409);
        }
        $user = (new User())->setName($name)->setEmail($email);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function read(int $identifier): User
    {
        $user = $this->userRepository->find($identifier);
        if (null === $user) {
            throw new NotFoundHttpException(sprintf('User ID %d not found', $identifier), code: 404);
        }

        return $user;
    }

    public function changeName(int $identifier, string $name): void
    {
        $user = $this->read($identifier);
        $user->setName($name);
        $this->entityManager->flush();
    }

    public function delete(int $identifier): void
    {
        $user = $this->read($identifier);
        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }

    public function assignToGroup(int $identifier, int $groupIdentifier): void
    {
        $user = $this->read($identifier);
        $group = $this->groupRepository->find($groupIdentifier);
        if (null === $group) {
            throw new NotFoundHttpException(sprintf('Group ID %d not found', $groupIdentifier), code: 404);
        }
        $inGroup = $user->getUserGroups()->contains($group);
        if ($inGroup) {
            $message = sprintf(
                'User %s (%d) already in Group %s (%d)',
                $user->getEmail(),
                $user->getId(),
                $group->getName(),
                $group->getId(),
            );
            throw new NotAcceptableHttpException($message, code: 406);
        }
        $user->addUserGroup($group);
        $this->entityManager->flush();
    }

    public function removeFromGroup(int $identifier, int $groupIdentifier): void
    {
        $user = $this->read($identifier);
        $group = $this->groupRepository->find($groupIdentifier);
        if (null === $group) {
            throw new NotFoundHttpException(sprintf('Group ID %d not found', $groupIdentifier), code: 404);
        }
        $inGroup = $user->getUserGroups()->contains($group);
        if (!$inGroup) {
            $message = sprintf(
                'User %s (%d) not in Group %s (%d)',
                $user->getEmail(),
                $user->getId(),
                $group->getName(),
                $group->getId(),
            );
            throw new NotAcceptableHttpException($message, code: 406);
        }
        $user->removeUserGroup($group);
        $this->entityManager->flush();
    }
}
