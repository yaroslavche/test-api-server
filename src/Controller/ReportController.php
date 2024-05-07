<?php

namespace App\Controller;

use App\Repository\GroupRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/report')]
class ReportController extends AbstractController
{
    #[Route('/group/{groupIdentifier}', name: 'app_report_group', requirements: ['groupIdentifier' => '\d+'])]
    public function group(
        GroupRepository $groupRepository,
        UserRepository $userRepository,
        SerializerInterface $serializer,
        int $groupIdentifier = 0,
    ): JsonResponse {
        if (0 === $groupIdentifier) {
            return new JsonResponse(
                data: $serializer->serialize($userRepository->findAll(), 'json'),
                status: 200,
                headers: [],
                json: true,
            );
        }

        $group = $groupRepository->find($groupIdentifier);
        if (null === $group) {
            throw new NotFoundHttpException(sprintf('Group id %d not found', $groupIdentifier));
        }

        return new JsonResponse(
            data: $serializer->serialize($userRepository->findByGroupIdentifier($groupIdentifier), 'json'),
            status: 200,
            headers: [],
            json: true,
        );
    }
}
