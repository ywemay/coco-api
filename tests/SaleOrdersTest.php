<?php

namespace App\Tests;

use App\Tests\ApiJWTTestCase;
use App\Entity\SaleOrder;
use App\Entity\PhysicalAddress;
use App\Entity\User;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class SaleOrdersTest extends ApiJWTTestCase
{
  use RefreshDatabaseTrait;

  protected function setUp()
  {
    $this->setIri('/api/orders');
  }

  public function testList(): void
  {
    $this->anonymousRequest();
    $this->assertResponseStatusCodeSame(401);

    $response = $this->customerRequest();
    $this->assertResponseStatusCodeSame(200);
    $this->assertSame(10, $response->toArray()['hydra:totalItems']);

    $this->teamleaderRequest();
    $this->assertResponseStatusCodeSame(403);

    $this->workerRequest();
    $this->assertResponseStatusCodeSame(403);

    $response = $this->adminRequest();
    $this->assertResponseStatusCodeSame(200);
    $this->assertSame(34, $response->toArray()['hydra:totalItems']);
  }

  public function testCreateOrder(): void
  {
    $this->createOrder('customer');
    $this->assertResponseStatusCodeSame(201);
    $this->createOrder('admin');
    $this->assertResponseStatusCodeSame(201);
    $this->createOrder('worker');
    $this->assertResponseStatusCodeSame(403);
    $this->createOrder('teamleader');
    $this->assertResponseStatusCodeSame(403);
  }

  private function getCreateJson($username)
  {
    $uiri = $this->findIriBy(User::class, ['username' => 'customer']);
    $address_iri = $this->findIriBy(PhysicalAddress::class, ['owner' => $uiri]);
    $json = [
      'date'=> date('Y-m-d'),
      'state' => 0,
      'address' => $address_iri,
      'containerType' => '20FT',
      'startDateTime' => date('Y-m-d H:i'),
      'price' => 700,
      'description' => 'This is a test record created by ' . $username
    ];

    if ($username == 'admin') {
      $json['owner'] = static::findIriBy(User::class,
        ['username' => 'customer']);
      $json['assignedTo'] = static::findIriBy(User::class,
        ['username' => 'teamleader']);
    }

    return $json;
  }

  private function createOrder($username) {
    $client = $this->getAuthenticatedClient($username);
    $json = $this->getCreateJson($username);
    return $client->request('POST', $this->getIri(), ['json' => $json]);
  }

  private function updateOrder($username) {
    $client = $this->getAuthenticatedClient($username);
    $uiri = $this->findIriBy(User::class, ['username' => 'customer']);
    $iri = static::findIriBy(SaleOrder::class, ['owner' => $uiri, 'state' => 0]);
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
    $uiri = $this->findIriBy(User::class, ['username' => 'customer']);
    $iri = static::findIriBy(SaleOrder::class, ['owner' => $uiri]);
    return $client->request('DELETE', $iri);
  }

  public function testDelete(): void
  {
    $response = $this->deleteOrder('worker');
    $this->assertResponseStatusCodeSame(403);
    $response = $this->deleteOrder('teamleader');
    $this->assertResponseStatusCodeSame(403);
    $response = $this->deleteOrder('customer');
    $this->assertResponseStatusCodeSame(403);
    $response = $this->deleteOrder('admin');
    $this->assertResponseStatusCodeSame(204);
  }
}
