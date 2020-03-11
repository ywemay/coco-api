<?php

namespace App\Tests;

use App\Tests\ApiJWTTestCase;
use App\Entity\User;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class UserAdminTest extends ApiJWTTestCase
{
  // This trait provided by HautelookAliceBundle will take care of refreshing the database content to a known state before each test
  use RefreshDatabaseTrait;

  public function testCreateUser(): void
  {
    $response = $this->getAuthenticatedClient('admin')->request('POST',
      '/api/users', ['json' => [
          'username' => 'testuser',
          'password' => 'testuser',
          'roles' => ['ROLE_TEAMLEADER'],
          'enabled' => true
    ]]);

    $this->assertResponseStatusCodeSame(201);
    $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

    $this->assertMatchesResourceItemJsonSchema(User::class);
    $this->assertSame('testuser', $response->toArray()['username']);
  }

  public function testCreateInvalidUser(): void
  {
    $token = $this->getAuthenticatedClient('admin')->request('POST', '/api/users',
    ['json' => [
      'username' => '',
      'password' => ''
      ]]);

    $this->assertResponseStatusCodeSame(400);
    $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
  }

  public function testUpdateUser(): void
  {
    $client = $this->getAuthenticatedClient('admin');

    $iri = static::findIriBy(User::class, ['username' => 'vasea']);

    $response = $client->request('PUT', $iri, ['json' => [
      'password' => 'vasea'
      ]]);
    $this->assertResponseIsSuccessful();
    $this->assertMatchesResourceCollectionJsonSchema(User::class);
  }

  public function testDeleteUser(): void
  {
    $client = $this->getAuthenticatedClient('admin');

    $iri = static::findIriBy(User::class, ['username' => 'vasea']);

    $client->request('DELETE', $iri);

    $this->assertResponseStatusCodeSame(204);
    $this->assertNull(
        // Through the container, you can access all your services from the tests, including the ORM, the mailer, remote API clients...
        static::$container->get('doctrine')->getRepository(User::class)->findOneBy(['username' => 'vasea'])
    );
  }

  public function testListsUsers(): void
  {
    $client = $this->getAuthenticatedClient('admin');
    $response = $client->request('GET', '/api/users');
    $this->assertResponseIsSuccessful();
  }
}
