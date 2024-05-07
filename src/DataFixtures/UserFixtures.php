<?php

namespace App\DataFixtures;

use App\Entity\Group;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        foreach ($this->userProvider() as $user) {
            $userEntity = new User();
            $userEntity
                ->setName($user['name'])
                ->setEmail($user['email'])
            ;
            foreach ($user['groups'] as $groupRef) {
                /** @var Group $group */
                $group = $this->getReference(sprintf('group_%d', $groupRef));
                $userEntity->addUserGroup($group);
            }
            $manager->persist($userEntity);
        }
        $manager->flush();
    }

    private function userProvider(): \Generator
    {
        yield ['name' => 'User 1', 'email' => 'user1@test.com', 'groups' => [1, 2]];
        yield ['name' => 'User 2', 'email' => 'user2@test.com', 'groups' => [2, 3]];
        yield ['name' => 'User 3', 'email' => 'user3@test.com', 'groups' => [3]];
        yield ['name' => 'User 4', 'email' => 'user4@test.com', 'groups' => [1]];
        yield ['name' => 'User 5', 'email' => 'user5@test.com', 'groups' => [1, 2]];
    }

    public function getDependencies(): iterable
    {
        return [
            GroupFixtures::class,
        ];
    }
}
