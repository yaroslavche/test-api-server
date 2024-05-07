<?php

namespace App\Service;

use App\Entity\User;

interface UserServiceInterface
{
    public function create(string $email, string $name): void;

    public function read(int $identifier): User;

    public function changeName(int $identifier, string $name): void;

    public function delete(int $identifier): void;

    public function assignToGroup(int $identifier, int $groupIdentifier): void;

    public function removeFromGroup(int $identifier, int $groupIdentifier): void;
}
