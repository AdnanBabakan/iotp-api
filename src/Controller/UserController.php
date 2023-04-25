<?php

namespace App\Controller;

use AngryBytes\Hash\Hash;
use AngryBytes\Hash\Hasher\Password;
use App\Entity\User;
use App\Util\JWT;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/register', name: 'user_register', methods: ['POST'])]
    public function register(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
    {
        $user = new User;
        $user->setPhone($request->get('phone'));
        $user->setFirstName($request->get('first_name'));
        $user->setLastName($request->get('last_name'));

        $hasher = new Hash(new Password());

        $user->setPassword($hasher->hash($request->get('password')));

        $errors = $validator->validate($user);

        if ($errors->count() > 0) {
            return $this->json([
                'status' => 'FAILED',
                'message' => 'SOMETHING_WENT_WRONG'
            ])->setStatusCode(422);
        }

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json([
            'status' => 'SUCCESSFUL',
            'message' => 'USER_CREATED_SUCCESSFULLY',
        ]);
    }

    #[Route('/authenticate', name: 'user_authenticate', methods: ['POST'])]
    public function authenticate(Request $request, EntityManagerInterface $entityManager)
    {
        $user = $entityManager->getRepository(User::class)->findOneBy([
            'phone' => $request->get('phone')
        ]);

        if (!$user) {
            return $this->json([
                'status' => 'FAILED',
                'message' => 'WRONG_CREDENTIALS'
            ]);
        }

        $hasher = new Hash(new Password());

        if (!$hasher->verify($request->get('password'), $user->getPassword())) {
            return $this->json([
                'status' => 'FAILED',
                'message' => 'WRONG_CREDENTIALS'
            ]);
        }

        return $this->json([
            'status' => 'SUCCESSFUL',
            'message' => 'USER_AUTHENTICATED_SUCCESSFULLY',
            'token' => (new JWT($user))->getToken()
        ]);
    }
}