<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Controller\interfaces\TokenAuthenticatedControllerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/x-user')]
class CommentController extends AbstractController implements TokenAuthenticatedControllerInterface
{
    #[Route('/{id}/comments', name: 'x_user_comment_create', methods: ['POST'])]
    public function create(int $id, Request $request, EntityManagerInterface $entityManager)
    {
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            return $this->json([
                'status' => 'FAILED',
                'message' => 'USER_NOT_FOUND'
            ])->setStatusCode(404);
        }

        $comment = new Comment;

        $comment->setUserId($user->getId());
        $comment->setUserId($request->attributes->get('user')->getId());
        $comment->setContent($request->get('content'));

        $entityManager->persist($comment);
        $entityManager->flush();

        return $this->json([
            'status' => 'SUCCESSFUL',
            'message' => 'COMMENT_CREATED_SUCCESSFULLY'
        ]);
    }
}