<?php

namespace App\Tests;

use App\Tests\ApiJWTTestCase;
use App\Entity\User;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class UsersTest extends ApiJWTTestCase
{
    // This trait provided by HautelookAliceBundle will take care of refreshing the database content to a known state before each test
    use RefreshDatabaseTrait;

    public function testGetUsersWithNoLogIn(): void
    {
      $client = static::createClient();
      $response = $client->request('GET', '/api/users');

      // Invalid token expected
      $this->assertSame(401, $response->getStatusCode());
    }

    public function testAdminListsUsers(): void
    {
      $token = $this->getToken();
      $client = static::createClient([],
        [
          //'auth_bearer' => $token,
          'headers' => [
            'Authorization' => 'Bearer ' . $token,
            'accept' => 'application/ld+json',
            'content-type' => 'application/json'
          ],
          'query' => [
            'token' => $token
          ]
        ]
      );
      $response = $client->request('GET', '/api/users');
      $this->assertSame(200, $response->getStatusCode());
    }

    public function testLogin(): void
    {
      $response = static::createClient()->request('POST', '/api/auth/login', ['json' => [
        'username' => 'admin',
        'password' => 'admin',
      ]]);

      $this->assertSame(200, $response->getStatusCode());
      $this->assertArrayHasKey('token', $response->toArray());
    }
}
