<?php

namespace App\Tests;

use App\Tests\ApiJWTTestCase;
use App\Entity\CustomerProfile;
use App\Entity\PhysicalAddress;
use App\Entity\User;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class PhysicalAddressTest extends ApiJWTTestCase
{
  use RefreshDatabaseTrait;

  private $em;

  protected function setUp()
  {
    $this->setIri('/api/address');
    $this->anonymousRequest();
    $this->em = self::$container->get('doctrine.orm.entity_manager');
  }

  public function testList(): void
  {
    $this->anonymousRequest();
    $this->assertResponseStatusCodeSame(401);

    $response = $this->customerRequest();
    $this->assertResponseStatusCodeSame(200);
    $this->assertSame(2, $response->toArray()['hydra:totalItems']);

    $this->teamleaderRequest();
    $this->assertResponseStatusCodeSame(403);

    $this->workerRequest();
    $this->assertResponseStatusCodeSame(403);

    $response = $this->adminRequest();
    $this->assertResponseStatusCodeSame(200);
    $this->assertSame(15, $response->toArray()['hydra:totalItems']);
  }

  public function testCreate(): void
  {
    $this->createItem('customer3');
    $this->assertResponseStatusCodeSame(201);
    $this->createItem('admin');
    $this->assertResponseStatusCodeSame(201);
    $this->createItem('worker');
    $this->assertResponseStatusCodeSame(403);
    $this->createItem('teamleader');
    $this->assertResponseStatusCodeSame(403);
  }

  private function getCreateJson($username)
  {
    $u = $this->em->getRepository(User::class)->findOneBy(['username' => 'customer']);

    $json = [
      'province' => 'Zhejiang',
      'city' => 'Yiwu',
      'street' => 'Some street',
      'address' => 'Some address',
      'customerProfile' => '/api/customer_profiles/' . $u->getCustomerProfile()->getId()
    ];

    return $json;
  }

  private function createItem($username) {
    $client = $this->getAuthenticatedClient($username);
    $json = $this->getCreateJson($username);
    return $client->request('POST', $this->getIri(), ['json' => $json]);
  }

  private function updateOrder($username) {
    $client = $this->getAuthenticatedClient($username);
    $u = $this->em->getRepository(User::class)->findOneBy(['username' => 'customer']);
    $iri = static::findIriBy(PhysicalAddress::class, ['customerProfile' => $u->getCustomerProfile()->getId()]);
    $json = $this->getCreateJson($username);
    return $client->request('PUT', $iri, ['json' => $json]);
  }

  public function testUpdate(): void
  {
    $response = $this->updateOrder('admin');
    $this->assertResponseStatusCodeSame(200);
    $response = $this->updateOrder('customer');
    $this->assertResponseStatusCodeSame(200);

    // customer shall not be able to update other customer's orders
    $response = $this->updateOrder('customer2');
    $this->assertResponseStatusCodeSame(404);

    // workers and team leaders shall not be able to update orders
    $response = $this->updateOrder('worker');
    $this->assertResponseStatusCodeSame(403);
    $response = $this->updateOrder('teamleader');
    $this->assertResponseStatusCodeSame(403);
  }

  public function deleteOrder($username) {
    $client = $this->getAuthenticatedClient($username);
    $u = $this->em->getRepository(User::class)->findOneBy(['username' => 'customer']);
    $iri = static::findIriBy(PhysicalAddress::class, ['customerProfile' => $u->getCustomerProfile()->getId()]);
    return $client->request('DELETE', $iri);
  }

  public function testDelete(): void
  {
    $response = $this->deleteOrder('worker');
    $this->assertResponseStatusCodeSame(403);
    $response = $this->deleteOrder('teamleader');
    $this->assertResponseStatusCodeSame(403);
    $response = $this->deleteOrder('customer');
    $this->assertResponseStatusCodeSame(500);
    $response = $this->deleteOrder('admin');
    $this->assertResponseStatusCodeSame(500);
  }
}
