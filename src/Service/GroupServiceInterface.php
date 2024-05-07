<?php

namespace App\Service;

use App\Entity\Group;

interface GroupServiceInterface
{
    public function create(string $name): void;

    public function read(int $identifier): Group;

    public function changeName(int $identifier, string $name): void;

    public function delete(int $identifier): void;
}
