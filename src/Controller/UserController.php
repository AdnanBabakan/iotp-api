<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
    #[Route('/user/register', name: 'user_register', methods: ['POST'])]
    public function register(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
    {
        $user = new User;
        $user->setPhone($request->get('phone'));
        $user->setFirstName($request->get('first_name'));
        $user->setLastName($request->get('last_name'));
        $user->setPassword($request->get('password'));

        $errors = $validator->validate($user);

        if($errors->count() > 0){
            return $this->json([
                'status' => 'FAILED',
                'message' => 'SOMETHING_WENT_WRONG'
            ]);
        }

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json([
            'status' => 'SUCCESSFUL',
            'message' => 'USER_CREATED_SUCCESSFULLY',
        ]);
    }
}