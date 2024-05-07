<?php

namespace App\Controller;

use App\Repository\GroupRepository;
use App\Service\GroupServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/groups')]
class GroupController extends AbstractController
{
    #[Route('/', name: 'app_groups_collection', methods: ['GET'])]
    public function list(GroupRepository $groupRepository, SerializerInterface $serializer): JsonResponse
    {
        return new JsonResponse(
            data: $serializer->serialize($groupRepository->findAll(), 'json'),
            status: 200,
            headers: [],
            json: true,
        );
    }

    #[Route('/', name: 'app_groups_create', methods: ['POST'])]
    public function create(Request $request, GroupServiceInterface $groupService): JsonResponse
    {
        $requestContent = json_decode($request->getContent(), true);
        $name = $requestContent['name'] ?? '';
        try {
            $groupService->create($name);
        } catch (\Throwable $exception) {
            return $this->json(['error' => $exception->getMessage()], $exception->getCode());
        }

        return $this->json([], 201);
    }

    #[Route('/{identifier}', name: 'app_groups_read', requirements: ['identifier' => '\d+'], methods: ['GET'])]
    public function read(
        int $identifier,
        GroupServiceInterface $groupService,
        SerializerInterface $serializer,
    ): JsonResponse {
        try {
            $group = $groupService->read($identifier);
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

    #[Route('/{identifier}/name', name: 'app_groups_change_name', requirements: ['identifier' => '\d+'], methods: ['PATCH'])]
    public function changeName(
        int $identifier,
        Request $request,
        GroupServiceInterface $groupService,
    ): JsonResponse {
        $requestContent = json_decode($request->getContent(), true);
        $name = $requestContent['name'] ?? '';
        try {
            $groupService->changeName($identifier, $name);
        } catch (\Throwable $exception) {
            return $this->json(['error' => $exception->getMessage()], $exception->getCode());
        }

        return $this->json([], 202);
    }

    #[Route('/{identifier}', name: 'app_groups_delete', requirements: ['identifier' => '\d+'], methods: ['DELETE'])]
    public function delete(
        int $identifier,
        GroupServiceInterface $groupService,
    ): JsonResponse {
        try {
            $groupService->delete($identifier);
        } catch (\Throwable $exception) {
            return $this->json(['error' => $exception->getMessage()], $exception->getCode());
        }

        return $this->json([], 202);
    }
}
