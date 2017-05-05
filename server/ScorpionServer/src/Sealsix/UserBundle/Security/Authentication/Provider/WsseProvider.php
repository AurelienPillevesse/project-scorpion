<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of WsseProvider
 *
 * @author Adam
 */

namespace Sealsix\UserBundle\Security\Authentication\Provider;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\NonceExpiredException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Sealsix\UserBundle\Security\Authentication\Token\WsseUserToken;
use Symfony\Component\Security\Core\Util\StringUtils;

class WsseProvider implements AuthenticationProviderInterface{
    private $userProvider;
    private $cacheDir;
    
    public function __construct(UserProviderInterface $userProvider, $cacheDir)
    {
        $this->userProvider = $userProvider;
        $this->cacheDir     = $cacheDir;
    }
    
    public function authenticate(TokenInterface $token) {
         $user = $this->userProvider->loadUserByUsername($token->getUsername());

        if ($user && $this->validateDigest($token->digest, $token->nonce, $token->created, $user->getPassword())) {
            $authenticatedToken = new WsseUserToken($user->getRoles());
            $authenticatedToken->setUser($user);

            return $authenticatedToken;
        }

        throw new AuthenticationException('The WSSE authentication failed.');
    }

    protected function validateDigest($digest, $nonce, $created, $secret)
    {
        if (strtotime($created) > time()) {
            return false;
        }

        if (time() - strtotime($created) > 300) {
            return false;
        }

        if (file_exists($this->cacheDir.'/'.$nonce) && file_get_contents($this->cacheDir.'/'.$nonce) + 300 > time()) {
            throw new NonceExpiredException('Previously used nonce detected');
        }
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0777, true);
        }
        file_put_contents($this->cacheDir.'/'.$nonce, time());

        $expected = base64_encode(sha1(base64_decode($nonce).$created.$secret, true));

        return StringUtils::equals($expected, $digest);
    }
    
    public function supports(TokenInterface $token) {
        return $token instanceof WsseUserToken;
    }

}
