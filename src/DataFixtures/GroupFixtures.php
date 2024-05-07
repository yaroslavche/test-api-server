<?php

namespace App\DataFixtures;

use App\Entity\Group;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class GroupFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        foreach ($this->groupProvider() as $group) {
            $groupEntity = new Group();
            $groupEntity->setName($group['name']);
            $this->addReference(sprintf('group_%d', $group['ref']), $groupEntity);
            $manager->persist($groupEntity);
        }
        $manager->flush();
    }

    private function groupProvider(): \Generator
    {
        yield ['ref' => 1, 'name' => 'Group 1'];
        yield ['ref' => 2, 'name' => 'Group 2'];
        yield ['ref' => 3, 'name' => 'Group 3'];
    }
}
