<?php

namespace App\Tests;

use App\Tests\ApiJWTTestCase;
use App\Entity\User;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class UsersTest extends ApiJWTTestCase
{
  // This trait provided by HautelookAliceBundle will take care of refreshing the database content to a known state before each test
  use RefreshDatabaseTrait;

  const REGISTER_URI = '/api/auth/register';

  protected function setUp()
  {
    $this->setIri('/api/users');
  }

  public function testList(): void
  {
    $this->anonymousRequest();
    $this->assertResponseStatusCodeSame(401);

    $response = $this->customerRequest();
    $this->assertResponseStatusCodeSame(403);

    $response = $this->teamleaderRequest();
    $this->assertResponseStatusCodeSame(200);
    $this->assertSame(32, $response ? $response->toArray()['hydra:totalItems'] : '');

    $this->workerRequest();
    $this->assertResponseStatusCodeSame(403);

    $response = $this->adminRequest();
    $this->assertResponseStatusCodeSame(200);
    $this->assertSame(45, $response->toArray()['hydra:totalItems']);
  }

  public function testLogin(): void
  {
    $this->getToken('worker');
    $this->assertResponseStatusCodeSame(200);
    $this->assertArrayHasKey('token', $response ? $response->toArray() : []);

    $this->getToken('worker_disabled');
    $this->assertResponseStatusCodeSame(401);
  }

  public function testRegisterWorker(): void
  {
    $response = static::createClient()->request('POST', self::REGISTER_URI . '/worker',
    ['json' => [
      'username' => 'dummywoker',
      'password' => 'dummyPass1',
      'phone' => '13599999999'
    ]]);
    $this->assertResponseStatusCodeSame(201);
  }

  public function testRegisterTeamLeader(): void
  {
    $response = static::createClient()->request('POST', self::REGISTER_URI . '/teamleader',
    ['json' => [
      'username' => 'dummytl',
      'password' => 'dummyPass1',
      'phone' => '13599999999'
    ]]);
    $this->assertResponseStatusCodeSame(201);
  }

  public function testRegisterCustomer(): void
  {
    $response = static::createClient()->request('POST', self::REGISTER_URI . '/customer',
    ['json' => [
      'username' => 'dummycustomer',
      'password' => 'dummyPass1',
      'phone' => '13599999999',
      'email' => 'dummycustomer@example.loc',
      'company' => 'Dummy Company'
    ]]);
    $this->assertResponseStatusCodeSame(201);
  }
}
