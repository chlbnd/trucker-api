<?php

namespace App\Security;

use App\Helper\ResponseFactory;
use App\Repository\UserRepository;
use Firebase\JWT\JWT;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class JwtAuthenticator extends AbstractGuardAuthenticator
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function supports(Request $request)
    {
        return (
            $request->getMethod() !== 'GET'
            && $request->getPathInfo() !== '/login'
        );
    }

    public function getCredentials(Request $request)
    {
        try {
            $token = str_replace(
                'Bearer ',
                '',
                $request->headers->get('Authorization')
            );
            return JWT::decode($token, $_ENV['JWT_KEY'], ['HS256']);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        if (!is_object($credentials)
            || !property_exists($credentials, 'email')
        ) {
            return null;
        }

        $user = $this->userRepository->findOneBy(
            ['email' => $credentials->email]
        );

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return (
            is_object($credentials)
            && property_exists($credentials, 'email')
        );
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $response = new ResponseFactory(
            ['message' => 'Authentication failure'],
            false,
            Response::HTTP_UNAUTHORIZED
        );

        return $response->getResponse();
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return null;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {

    }

    public function supportsRememberMe()
    {
        return false;
    }
}
