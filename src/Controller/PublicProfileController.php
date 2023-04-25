<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/users')]
class PublicProfileController extends AbstractController
{
    #[Route('/', name: 'users_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): JsonResponse
    {
        $users = $entityManager->getRepository(User::class)->findAll();

        foreach ($users as &$user) {
            $user = [
                'id' => $user->getId(),
                'first_name' => $user->getFirstName(),
                'last_name' => $user->getLastName(),
                'profile_link' => '/users/' . $user->getId()
            ];
        }

        return $this->json($users);
    }

    #[Route('/{id}', name: 'users_show', methods: ['GET'])]
    public function show(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $entityManager->getRepository(User::class)->find($id);

        if(!$user) {
            return $this->json([
                'status' => 'FAILED',
                'message' => 'USER_NOT_FOUND'
            ])->setStatusCode(404);
        }

        return $this->json([
            'id' => $user->getId(),
            'first_name' => $user->getFirstName(),
            'last_name' => $user->getLastName(),
            'profile_link' => '/users/' . $user->getId()
        ]);
    }
}