<?php

namespace App\Tests;

use App\Tests\ApiJWTTestCase;
use App\Entity\SaleOrderItem;
use App\Entity\SaleOrder;
use App\Entity\Company;
use App\Entity\User;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class SaleOrderItemsTest extends ApiJWTTestCase
{
  use RefreshDatabaseTrait;

  const IRI = '/api/order_items';

  public function testList(): void
  {
    $response = $this->userRequest('orange', self::IRI);
    $this->assertResponseStatusCodeSame(200);
    $this->assertSame(6, $response->toArray()['hydra:totalItems']);

    $response = $this->userRequest('pekya', self::IRI);
    $this->assertResponseStatusCodeSame(403);

    $response = $this->userRequest('vasea', self::IRI);
    $this->assertResponseStatusCodeSame(403);

    $response = $this->userRequest('admin', self::IRI);
    $this->assertResponseStatusCodeSame(200);
    // $this->assertSame(12, $response->toArray()['hydra:totalItems']);
  }

  public function testCreate(): void
  {
    $this->createOrderItem('orange');
    $this->assertResponseStatusCodeSame(201);
    $this->createOrderItem('admin');
    $this->assertResponseStatusCodeSame(201);
    $this->createOrderItem('vasea');
    $this->assertResponseStatusCodeSame(403);
    $this->createOrderItem('pekya');
    $this->assertResponseStatusCodeSame(403);
  }

  public function testUpdate(): void
  {
    $response = $this->updateOrderItem('admin');
    $this->assertResponseStatusCodeSame(200);
    $response = $this->updateOrderItem('orange');
    $this->assertResponseStatusCodeSame(200);

    // customer shall not be able to update other customer's orders
    $response = $this->updateOrderItem('purple');
    $this->assertResponseStatusCodeSame(404);

    // workers and team leaders shall not be able to update orders
    $response = $this->updateOrderItem('vasea');
    $this->assertResponseStatusCodeSame(403);
    $response = $this->updateOrderItem('pekya');
    $this->assertResponseStatusCodeSame(403);
  }

  public function testDelete(): void
  {
    $response = $this->deleteOrderItem('pekya');
    $this->assertResponseStatusCodeSame(403);
    $response = $this->deleteOrderItem('vasea');
    $this->assertResponseStatusCodeSame(403);
    $response = $this->deleteOrderItem('orange');
    $this->assertResponseStatusCodeSame(204);
    $response = $this->deleteOrderItem('admin');
    $this->assertResponseStatusCodeSame(204);
  }

  private function getCompanyIri() {
    $iri = static::findIriBy(User::class, ['username' => 'orange']);
    $parts = explode("/", $iri);
    return static::findIriBy(Company::class, ['owner' => $parts[3]]);
  }

  private function getSaleOrderIri() {
    $id = $this->iriToId($this->getCompanyIri());
    $iri = static::findIriBy(SaleOrder::class, ['company' => $id]);
    return $iri;
  }

  private function createOrderItem($username) {
    $now =date("Y-m-d h:i:s");
    $client = $this->getAuthenticatedClient($username);
    $response = $client->request('POST', self::IRI,['json' => [
      'saleOrder' => $this->getSaleOrderIri(),
      'containerType' => 'HQ',
      'startDateTime' => $now,
      'price' => 50000,
      'description' => 'Test orderd item'
      ]]);
  }

  private function updateOrderItem($username) {
    $client = $this->getAuthenticatedClient($username);
    $soid = $this->iriToId($this->getSaleOrderIri());
    $iri = static::findIriBy(SaleOrderItem::class, ['saleOrder' => $soid]);
    return $client->request('PUT', $iri, ['json' => [
      'price' => 600000,
      'description' => 'Updated description for test order item'
      ]]);
  }

  public function deleteOrderItem($username) {
    $client = $this->getAuthenticatedClient($username);
    $soid = $this->iriToId($this->getSaleOrderIri());
    $iri = static::findIriBy(SaleOrderItem::class, ['saleOrder' => $soid]);
    return $client->request('DELETE', $iri);
  }
}
