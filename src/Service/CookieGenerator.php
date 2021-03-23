<?php


namespace App\Service;

use App\Entity\Account;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key;
use Symfony\Component\HttpFoundation\Cookie;

class CookieGenerator
{
    private $secret;

    public function __construct(string $secret)
    {
        $this->secret = $secret;
    }

    public function generate(Account $account)
    {
        $token = (new Builder())
            ->withClaim('mercure', ['subscribe' => [
                'https://skillfoot.fr/users/chat',
                'https://skillfoot.fr/users']
            ])
            ->getToken(new Sha256(), new Key($this->secret));

        return "mercureAuthorization={$token}; Path=/.well-known/mercure; secure; HttpOnly;";
//        return Cookie::create('mercureAuthorization', $token, 0, 'http://localhost:3000/.well-known/mercure');
    }
}
