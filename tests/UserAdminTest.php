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
      $token = $this->getToken('admin');

      $response = static::createClient([],
        [
          'auth_bearer' => $token,
          'headers' => [
            'accept' => 'application/ld+json'
          ]
        ])->request('POST',
        '/api/users', ['json' => [
            'username' => 'testuser',
            'password' => 'testuser',
            'roles' => ['ROLE_TEAMLEADER'],
            'enabled' => true
      ]]);

      $this->assertResponseStatusCodeSame(201);
      $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

      $this->assertJsonContains([
        '@context' => '/contexts/User',
        '@type' => 'User',
        'username' => 'testuser',
        'enabled' => true
      ]);
      $this->assertMatchesResourceItemJsonSchema(User::class);
    }

    public function testCreateInvalidUser(): void
    {
      $token = $this->getToken('admin');

      $response = static::createClient([],
        [
          'auth_bearer' => $token,
          'headers' => [
            'accept' => 'application/ld+json'
          ]
        ])->request('POST', '/api/users',
      ['json' => [
        'username' => '',
        'password' => ''
        ]]);

      $this->assertResponseStatusCodeSame(400);
      $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testUpdateUser(): void
    {
      $token = $this->getToken('admin');
      $client = static::createClient([],
        [
          'auth_bearer' => $token,
          'headers' => [
            'accept' => 'application/ld+json'
          ]
        ]);

      $iri = static::findIriBy(User::class, ['username' => 'vasea']);

      $client->request('PUT', $iri, ['json' => [
        'password' => 'vasea'
        ]]);
      $this->assertResponseIsSuccessful();
      $this->assertJsonContains([
        '@id' => $iri,
        'username' => 'vasea'
      ]);
    }

    public function testDeleteUser(): void
    {
      $token = $this->getToken('admin');
      $client = static::createClient([],
        [
          'auth_bearer' => $token,
          'headers' => [
            'accept' => 'application/ld+json'
          ]
        ]);
      $iri = static::findIriBy(User::class, ['username' => 'vasea']);

      $client->request('DELETE', $iri);

      $this->assertResponseStatusCodeSame(204);
      $this->assertNull(
          // Through the container, you can access all your services from the tests, including the ORM, the mailer, remote API clients...
          static::$container->get('doctrine')->getRepository(User::class)->findOneBy(['username' => 'vasea'])
      );
    }
}
