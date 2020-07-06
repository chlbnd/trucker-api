<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Firebase\JWT\JWT;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class LoginController extends AbstractController
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(
        UserRepository $repository, 
        UserPasswordEncoderInterface $encoder
    ){
        $this->repository = $repository;
        $this->encoder = $encoder;
    }

    public function index(Request $request)
    {
        try {
            $loginData = json_decode($request->getContent());

            $user = $this->repository->findOneBy([
                'email' => $loginData->email
            ]);

            if (!$user) {
                throw new \Exception;
            }

            if (!$this->encoder->isPasswordValid($user, $loginData->password)) {
                throw new \Exception;
            }
        } catch(\Exception $e) {
            return new JsonResponse(
                [
                    'error' => 'Check user and password and try again.'
                ],
                Response::HTTP_UNAUTHORIZED
            );
        }

        $token = JWT::encode(
            ['email' => $user->getEmail()],
            $_ENV['JWT_KEY']
        );
        return new JsonResponse(['access_token' => $token]);
    }
}