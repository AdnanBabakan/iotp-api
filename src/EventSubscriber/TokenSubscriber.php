<?php

namespace App\EventSubscriber;

use App\Controller\interfaces\TokenAuthenticatedControllerInterface;
use App\Entity\User;
use App\Util\JWT;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class TokenSubscriber implements EventSubscriberInterface
{
    private $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    public function onKernelController(ControllerEvent $event)
    {
        $controller = $event->getController();

        if (is_array($controller)) {
            $controller = $controller[0];
        }

        if (!($controller instanceof TokenAuthenticatedControllerInterface)) {
            return;
        }

        $token = explode(' ', $event->getRequest()->headers->get('Authorization'))[1];

        if (!$token) {
            throw new AccessDeniedHttpException('INVALID_TOKEN');
        }

        $token_exploded = explode('.', $token);

        $user_data = json_decode(base64_decode($token_exploded[1]));

        $user = $this->entityManager->getRepository(User::class)->find($user_data->user_id);

        if (!$user) {
            throw new AccessDeniedHttpException('INVALID_TOKEN');
        }

        if (!(new JWT($user))->matches($token)) {
            throw new AccessDeniedHttpException('INVALID_TOKEN');
        }

        $event->getRequest()->attributes->set('user', $user);
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController'
        ];
    }
}