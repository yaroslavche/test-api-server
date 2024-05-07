<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Group;
use App\Repository\GroupRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Service\Attribute\Required;

final class GroupService implements GroupServiceInterface
{
    public function __construct(
        #[Required] public GroupRepository $groupRepository,
        #[Required] public UserRepository $userRepository,
        #[Required] public EntityManagerInterface $entityManager,
    ) {
    }

    public function create(string $name): void
    {
        if ('' === $name) {
            throw new \InvalidArgumentException('Group name must be not empty', 400);
        }
        $existsGroup = $this->groupRepository->findOneBy(['name' => $name]);
        if (null !== $existsGroup) {
            throw new \InvalidArgumentException(sprintf('Group with name "%s" already exists', $name), 409);
        }
        $group = (new Group())->setName($name);
        $this->entityManager->persist($group);
        $this->entityManager->flush();
    }

    public function read(int $identifier): Group
    {
        $group = $this->groupRepository->find($identifier);
        if (null === $group) {
            throw new NotFoundHttpException(sprintf('Group ID %d not found', $identifier), code: 404);
        }

        return $group;
    }

    public function changeName(int $identifier, string $name): void
    {
        $group = $this->read($identifier);
        $existsGroup = $this->groupRepository->findOneBy(['name' => $name]);
        if (null !== $existsGroup) {
            throw new \InvalidArgumentException(sprintf('Group with name "%s" already exists', $name), 409);
        }
        $group->setName($name);
        $this->entityManager->flush();
    }

    public function delete(int $identifier): void
    {
        $group = $this->read($identifier);
        $users = $this->userRepository->findByGroupIdentifier($group->getId() ?? 0);
        if ([] !== $users) {
            throw new NotAcceptableHttpException('Group contains users and can\'t be deleted', code: 406);
        }
        $this->entityManager->remove($group);
        $this->entityManager->flush();
    }
}
