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

    public function prePersist(User $data)
    {
      if ($data->getPlainPassword()) {
          $data->setPassword(
              $this->userPasswordEncoder->encodePassword($data, $data->getPlainPassword())
          );
          $data->eraseCredentials();
      }

      $request = $this->requestStack->getCurrentRequest();
      $route = $request ? $request->attributes->get('_route') : '';
      if ($route == 'api_users_regcustomer_collection') {
        $data->setRoles(['ROLE_USER', 'ROLE_CUSTOMER']);
      }
      elseif ($route == 'api_users_regteamleader_collection') {
        $data->setRoles(['ROLE_USER', 'ROLE_TEAMLEADER']);
      }
      elseif ($route == 'api_users_regworker_collection') {
        $data->setRoles(['ROLE_USER', 'ROLE_WORKER']);
      }
      // print_r($data);
    }
}
