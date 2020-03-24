<?php

namespace App\Doctrine;

use App\Entity\User;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class UserListener
{
    private $security;
    private $userPasswordEncoder;
    private $requestStack;

    public function __construct(Security $security, UserPasswordEncoderInterface $userPasswordEncoder, RequestStack $requestStack)
    {
      $this->security = $security;
      $this->userPasswordEncoder = $userPasswordEncoder;
      $this->requestStack = $requestStack;
    }

    public function prePersist(User $user)
    {
      if ($user->getPlainPassword()) {
          $user->setPassword(
              $this->userPasswordEncoder->encodePassword($user, $user->getPlainPassword())
          );
          $user->eraseCredentials();
      }

      $request = $this->requestStack->getCurrentRequest();
      $route = $request ? $request->attributes->get('_route') : '';
      if ($route == 'api_users_regcustomer_collection') {
        $user->setRoles(['ROLE_USER', 'ROLE_CUSTOMER']);
      }
      elseif ($route == 'api_users_regteamleader_collection') {
        $user->setRoles(['ROLE_USER', 'ROLE_TEAMLEADER']);
      }
      elseif ($route == 'api_users_regworker_collection') {
        $user->setRoles(['ROLE_USER', 'ROLE_WORKER']);
      }
    }
}
