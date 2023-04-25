<?php

namespace App\Controller;

use AngryBytes\Hash\Hash;
use AngryBytes\Hash\Hasher\Password;
use App\Controller\interfaces\TokenAuthenticatedControllerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/x-user')]
class ProfileController extends AbstractController implements TokenAuthenticatedControllerInterface
{
    #[Route('/', name: 'x_user_index', methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $entityManager)
    {
        $user = $request->attributes->get('user');

        return $this->json([
            'id' => $user->getId(),
            'first_name' => $user->getFirstName(),
            'last_name' => $user->getLastName(),
        ]);
    }

    #[Route('/', name: 'x_user_patch', methods: ['POST'])]
    public function patch(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
    {
        $user = $request->attributes->get('user');

        $user->setFirstName($request->get('first_name'));
        $user->setLastName($request->get('last_name'));

        if($request->get('password')) {
            $hasher = new Hash(new Password());

            $user->setPassword($hasher->hash($request->get('password')));
        }

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
            'message' => 'USER_UPDATED_SUCCESSFULLY'
        ]);
    }
}