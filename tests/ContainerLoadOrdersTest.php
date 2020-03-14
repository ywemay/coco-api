<?php

namespace App\Tests;

use App\Tests\ApiJWTTestCase;
use App\Entity\ContainerLoadOrder;
use App\Entity\User;
use App\Entity\SaleOrderItem;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class ContainerLoadOrdersTest extends ApiJWTTestCase
{
  use RefreshDatabaseTrait;

  const IRI = '/api/clorders';

  public function testList(): void
  {
    // customers cannot access container load orders, or shall they?
    $response = $this->userRequest('orange', self::IRI);
    $this->assertResponseStatusCodeSame(403);

    // workers cannot access container load ordders
    $response = $this->userRequest('pekya', self::IRI);
    $this->assertResponseStatusCodeSame(403);

    // team leader can access assigned to him container load orders
    $response = $this->userRequest('vasea', self::IRI);
    $this->assertResponseStatusCodeSame(200);
    $this->assertSame(3, $response->toArray()['hydra:totalItems']);

    // admin can access all orders
    $response = $this->userRequest('admin', self::IRI);
    $this->assertResponseStatusCodeSame(200);
    $this->assertSame(6, $response->toArray()['hydra:totalItems']);
  }

  public function testCreate(): void
  {
    $this->createOrder('orange');
    $this->assertResponseStatusCodeSame(403);
    $this->createOrder('admin');
    $this->assertResponseStatusCodeSame(201);
    $this->createOrder('vasea');
    $this->assertResponseStatusCodeSame(403);
    $this->createOrder('pekya');
    $this->assertResponseStatusCodeSame(403);
  }

  private function createOrder($username) {
    $client = $this->getAuthenticatedClient($username);
    $iri = static::findIriBy(SaleOrder::class, ['company' => $companyId]);
    $response = $client->request('POST', self::IRI, ['json' => [
      'date'=>date('Y-m-d'),
      'company' => $this->getCompanyIri(),
      'state' => 1
      ]]);
  }

  private function updateOrder($username) {
    $client = $this->getAuthenticatedClient($username);
    $companyId = $this->iriToId($this->getCompanyIri());
    $iri = static::findIriBy(SaleOrder::class, ['company' => $companyId]);
    return $client->request('PUT', $iri, ['json' => [
      'date'=>date('Y-m-d'),
      'company' => $this->getCompanyIri()
      ]]);
  }

  public function testUpdateOrder(): void
  {
    $response = $this->updateOrder('admin');
    $this->assertResponseStatusCodeSame(200);
    $response = $this->updateOrder('orange');
    $this->assertResponseStatusCodeSame(200);

    // customer shall not be able to update other customer's orders
    $response = $this->updateOrder('purple');
    $this->assertResponseStatusCodeSame(404);

    // workers and team leaders shall not be able to update orders
    $response = $this->updateOrder('vasea');
    $this->assertResponseStatusCodeSame(403);
    $response = $this->updateOrder('pekya');
    $this->assertResponseStatusCodeSame(403);
  }

  public function deleteOrder($username) {
    $client = $this->getAuthenticatedClient($username);
    $companyId = $this->iriToId($this->getCompanyIri());
    $iri = static::findIriBy(SaleOrder::class, ['company' => $companyId]);
    return $client->request('DELETE', $iri);
  }

  /*
  Not working since sale_order_items point to the sale_order
  need to delete related sale_order_items first

  public function testDeleteOrder(): void
  {
    $response = $this->deleteOrder('pekya');
    $this->assertResponseStatusCodeSame(403);
    $response = $this->deleteOrder('vasea');
    $this->assertResponseStatusCodeSame(403);
    $response = $this->deleteOrder('orange');
    $this->assertResponseStatusCodeSame(403);
    $response = $this->deleteOrder('admin');
    $this->assertResponseStatusCodeSame(204);
  }
  */
}
