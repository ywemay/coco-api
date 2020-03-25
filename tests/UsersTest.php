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
    // $this->assertArrayHasKey('token', $response ? $response->toArray() : []);

    $this->getToken('worker_disabled');
    $this->assertResponseStatusCodeSame(401);
  }

  public function testRegister(): void
  {
    $response = static::createClient()->request('POST',
      self::REGISTER_URI . '/worker',
      ['json' => [
        'username' => 'dummywoker',
        'password' => 'dummyPass1',
        'phone' => '13599999999'
      ]]);
    $this->assertResponseStatusCodeSame(201);

    $response = static::createClient()->request('POST',
      self::REGISTER_URI . '/teamleader',
      ['json' => [
        'username' => 'dummytl',
        'password' => 'dummyPass1',
        'phone' => '13599999999'
      ]]);
    $this->assertResponseStatusCodeSame(201);

    $response = static::createClient()->request('POST',
      self::REGISTER_URI . '/customer',
      ['json' => [
        'username' => 'dummycustomer',
        'password' => 'dummyPass1',
        'phone' => '13599999999',
        'email' => 'dummycustomer@example.loc',
        'company' => 'Dummy Company'
      ]]);

    $this->assertResponseStatusCodeSame(201);
  }

  public function testCreate(): void
  {
    // Admin creates a valid user:
    list($response, $client) = $this->adminRequest(['POST'], $this->getIri(), ['json' => [
      'username' => 'testuser',
      'password' => 'testuser',
      'roles' => ['ROLE_TEAMLEADER'],
      'phone' => '1230984701293',
      'enabled' => true
    ]]);
    $this->assertResponseStatusCodeSame(201);
    $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    $this->assertMatchesResourceItemJsonSchema(User::class);
    $this->assertSame('testuser', $response->toArray()['username']);

    $response = $this->adminRequest('POST', $this->getIri(),['json' => [
      'username' => '',
      'password' => '',
    ]]);

    $this->assertResponseStatusCodeSame(400);
    $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
  }

  public function testUpdate(): void
  {
    $client = $this->getAuthenticatedClient('admin');

    $iri = static::findIriBy(User::class, ['username' => 'teamleader']);

    $response = $client->request('PUT', $iri, ['json' => [
      'username' => 'teamleader',
      'password' => 'teamleader',
      'enabled' => true,
      'roles' => ['ROLE_TEAMLEADER', 'ROLE_WORKER'],
      'phone' => '12343423443',
      'email' => 'teamleader@teamleader.loc'
      ]]);
    $this->assertResponseIsSuccessful();
    $this->assertMatchesResourceCollectionJsonSchema(User::class);
  }

  public function testDelete(): void
  {
    $client = $this->getAuthenticatedClient('admin');

    $iri = static::findIriBy(User::class, ['username' => 'teamleader_disabled']);

    $client->request('DELETE', $iri);

    $this->assertResponseStatusCodeSame(204);
    $this->assertNull(
        // Through the container, you can access all your services from the tests, including the ORM, the mailer, remote API clients...
        static::$container->get('doctrine')->getRepository(User::class)->findOneBy(['username' => 'vasea'])
    );
  }
}
