<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Service\UserServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/users')]
class UserController extends AbstractController
{
    #[Route('/', name: 'app_users_collection', methods: ['GET'])]
    public function list(UserRepository $userRepository, SerializerInterface $serializer): JsonResponse
    {
        return new JsonResponse(
            data: $serializer->serialize($userRepository->findAll(), 'json'),
            status: 200,
            headers: [],
            json: true,
        );
    }

    #[Route('/', name: 'app_users_create', methods: ['POST'])]
    public function create(Request $request, UserServiceInterface $userService): JsonResponse
    {
        $requestContent = json_decode($request->getContent(), true);
        $email = $requestContent['email'] ?? '';
        $name = $requestContent['name'] ?? '';
        try {
            $userService->create($email, $name);
        } catch (\Throwable $exception) {
            return $this->json(['error' => $exception->getMessage()], $exception->getCode());
        }

        return $this->json([], 201);
    }

    #[Route('/{identifier}', name: 'app_users_read', requirements: ['identifier' => '\d+'], methods: ['GET'])]
    public function read(
        int $identifier,
        UserServiceInterface $userService,
        SerializerInterface $serializer,
    ): JsonResponse {
        try {
            $group = $userService->read($identifier);
        } catch (\Throwable $exception) {
            return $this->json(['error' => $exception->getMessage()], $exception->getCode());
        }

        return new JsonResponse(
            data: $serializer->serialize($group, 'json'),
            status: 200,
            headers: [],
            json: true,
        );
    }

    #[Route('/{identifier}/name', name: 'app_users_change_name', requirements: ['identifier' => '\d+'], methods: ['PATCH'])]
    public function changeName(
        int $identifier,
        Request $request,
        UserServiceInterface $userService,
    ): JsonResponse {
        $requestContent = json_decode($request->getContent(), true);
        $name = $requestContent['name'] ?? '';
        try {
            $userService->changeName($identifier, $name);
        } catch (\Throwable $exception) {
            return $this->json(['error' => $exception->getMessage()], $exception->getCode());
        }

        return $this->json([], 202);
    }

    #[Route('/{identifier}', name: 'app_users_delete', requirements: ['identifier' => '\d+'], methods: ['DELETE'])]
    public function delete(
        int $identifier,
        UserServiceInterface $userService,
    ): JsonResponse {
        try {
            $userService->delete($identifier);
        } catch (\Throwable $exception) {
            return $this->json(['error' => $exception->getMessage()], $exception->getCode());
        }

        return $this->json([], 202);
    }

    #[Route(
        '/{identifier}/assign-to-group/{groupIdentifier}',
        name: 'app_users_assign_to_group',
        requirements: ['identifier' => '\d+', 'groupIdentifier' => '\d+'],
        methods: ['PATCH'],
    )]
    public function assignToGroup(
        int $identifier,
        int $groupIdentifier,
        UserServiceInterface $userService,
    ): JsonResponse {
        try {
            $userService->assignToGroup($identifier, $groupIdentifier);
        } catch (\Throwable $exception) {
            return $this->json(['error' => $exception->getMessage()], $exception->getCode());
        }

        return $this->json([], 202);
    }

    #[Route(
        '/{identifier}/remove-from-group/{groupIdentifier}',
        name: 'app_users_remove_from_group',
        requirements: ['identifier' => '\d+', 'groupIdentifier' => '\d+'],
        methods: ['PATCH'],
    )]
    public function removeFromGroup(
        int $identifier,
        int $groupIdentifier,
        UserServiceInterface $userService,
    ): JsonResponse {
        try {
            $userService->removeFromGroup($identifier, $groupIdentifier);
        } catch (\Throwable $exception) {
            return $this->json(['error' => $exception->getMessage()], $exception->getCode());
        }

        return $this->json([], 202);
    }
}
