<?php

namespace App\Controller;

use App\Controller\interfaces\TokenAuthenticatedControllerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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
}