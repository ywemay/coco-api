<?php
namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class UserDataPersister implements DataPersisterInterface
{
    private $entityManager;
    private $userPasswordEncoder;
    private $requestStack;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $userPasswordEncoder, RequestStack $requestStack)
    {
        $this->entityManager = $entityManager;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->requestStack = $requestStack;
    }

    public function supports($data): bool
    {
        return $data instanceof User;
    }

    /**
     * @param User $data
     */
    public function persist($data)
    {
        if ($data->getPlainPassword()) {
            $data->setPassword(
                $this->userPasswordEncoder->encodePassword($data, $data->getPlainPassword())
            );
            $data->eraseCredentials();
        }

        $request = $this->requestStack->getCurrentRequest();
        $route = $request->attributes->get('_route');
        if ($route == 'api_users_regcustomer_collection') {
          $data->setRoles(['ROLE_USER', 'ROLE_CUSTOMER']);
        }
        elseif ($route == 'api_users_regteamleader_collection') {
          $data->setRoles(['ROLE_USER', 'ROLE_TEAMLEADER']);
        }
        elseif ($route == 'api_users_regworker_collection') {
          $data->setRoles(['ROLE_USER', 'ROLE_WORKER']);
        }

        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }

    public function remove($data)
    {
        $this->entityManager->remove($data);
        $this->entityManager->flush();
    }
}
