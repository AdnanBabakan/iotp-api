<?php

namespace App\Util;

use App\Entity\User;
use JetBrains\PhpStorm\ArrayShape;
use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

class JWT
{
    public User $user;
    private string $secret;

    public function __construct(User $user) {
        $this->user = $user;
        $this->secret = 'e3d778cd-3c1b-4358-bbad-1426f5759fb9';
    }

    #[ArrayShape(['typ' => "string", 'alg' => "string"])]
    public function getHeader()
    {
        return [
            'typ' => 'jwt',
            'alg' => 'custom'
        ];
    }

    #[ArrayShape(['user_type' => "string", 'user_id' => "int|null"])]
    public function getPayload()
    {
        return [
            'user_type' => 'user',
            'user_id' => $this->user->getId()
        ];
    }

    public function getSignature()
    {
        return base64_encode(md5($this->getFirstPart() . '/' . $this->secret));
    }

    public function getFirstPart()
    {
        return self::seralize($this->getHeader()) . '.' . self::seralize($this->getPayload());
    }

    public function getToken()
    {
        return $this->getFirstPart() . '.' . $this->getSignature();
    }

    public function matches(string $token)
    {
        return $this->getToken() === $token;
    }

    private static function seralize(array $obj) {
        return base64_encode(json_encode($obj));
    }
}