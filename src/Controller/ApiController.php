<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/auth")
 */
class ApiController extends AbstractController
{
    private $passwordEncoder;
    private $manager;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $manager)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->manager = $manager;
    }

    /**
     * @Route("/login", name="login", methods={"POST"})
     */
    public function login(Request $request)
    {
        $user = $this->getUser();

        return $this->json([
            'username' => $user->getUsername(),
            'roles' => $user->getRoles(),
        ]);
    }

    /**
     * @Route("/logout", name="app_logout", methods={"GET"})
     */
    public function logout()
    {
        // controller can be blank: it will never be executed!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }

    /**
     * @Route("/user", methods={"GET"})
     */
    public function user(){
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['username' => "anonymous", 'id' => 0], 200);
        }
        return new JsonResponse(['username' => $user->getUsername()], 200);
    }

    /**
     * @Route("/register", name="api_auth_register",  methods={"POST"})
     * @param Request $request
     * @param UserManagerInterface $userManager
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function register(Request $request, ValidatorInterface $validator)
    {
        $data = json_decode(
            $request->getContent(),
            true
        );

        if (empty($data)) {
            return new JsonResponse(["error" => 'Data expected'], 500);
        }
        elseif (!isset($data['username'])) {
            return new JsonResponse(["error" => 'Username expected'], 500);
        }
        elseif (strlen($data['username']) < 5 ) {
            return new JsonResponse(["error" => 'Username shall be 5 or more characters in length'], 500);
        }
        elseif (!isset($data['password'])) {
            return new JsonResponse(["error" => 'Password expected'], 500);
        }
        elseif (strlen($data['password']) < 5 ) {
            return new JsonResponse(["error" => 'Password shall be 5 or more characters in length'], 500);
        }

        $username = $data['username'];
        $password = $data['password'];
        // $email = $data['email'];

        $user = new User();

        $user
            ->setUsername($username)
            ->setRoles(['ROLE_USER'])
        ;
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            $password
        ));

        $errors = $validator->validate($user);
        if(count($errors) > 0) {
            $error = $errors->get(0);
            return new JsonResponse([
                "error" => $error->getMessage(),
                'code' => $error->getCode()
            ], 500);
        }

        try {
            $this->manager->persist($user);
            $this->manager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(["error" => $e->getMessage()], 500);
        }

        return new JsonResponse(["success" => $user->getUsername(). " has been registered!"], 200);
    }
}
