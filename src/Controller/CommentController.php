<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Controller\interfaces\TokenAuthenticatedControllerInterface;

class CommentController extends AbstractController implements TokenAuthenticatedControllerInterface
{

}