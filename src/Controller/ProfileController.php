<?php

namespace App\Controller;

use App\Controller\interfaces\TokenAuthenticatedControllerInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use http\Env\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/x-user')]
class ProfileController extends AbstractController implements TokenAuthenticatedControllerInterface
{
    #[Route('/', name: 'x_user_index', methods: ['GET'])]
    public function index(\Symfony\Component\HttpFoundation\Request $request, EntityManagerInterface $entityManager)
    {
        $user = $request->attributes->get('user');

        return $this->json([
            'id' => $user->getId(),
            'first_name' => $user->getFirstName(),
            'last_name' => $user->getLastName(),
        ]);
    }
}