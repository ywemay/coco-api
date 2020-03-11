<?php

namespace App\Tests;

use App\Tests\ApiJWTTestCase;
use App\Entity\User;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class UsersTest extends ApiJWTTestCase
{
    // This trait provided by HautelookAliceBundle will take care of refreshing the database content to a known state before each test
    use RefreshDatabaseTrait;

    public function testGetUsersWithoutLoggingIn(): void
    {
      $client = static::createClient();
      $response = $client->request('GET', '/api/users');

      $this->assertSame(401, $response->getStatusCode());
    }

    public function testAdminListsUsers(): void
    {
      $token = $this->getToken();
      $client = static::createClient([],
        [
          'auth_bearer' => $token,
          'headers' => [
            'accept' => 'application/ld+json'
          ]
        ]
      );
      $response = $client->request('GET', '/api/users');
      $this->assertResponseIsSuccessful();
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

    public function testRegisterWorker(): void
    {
      $response = static::createClient()->request('POST', '/api/auth/register/worker',
      ['json' => [
        'username' => 'dummywoker',
        'password' => 'dummyPass1',
        'phone' => '13599999999'
      ]]);
      $this->assertResponseStatusCodeSame(201);
    }

    public function testRegisterTeamLeader(): void
    {
      $response = static::createClient()->request('POST', '/api/auth/register/teamleader',
      ['json' => [
        'username' => 'dummytl',
        'password' => 'dummyPass1',
        'phone' => '13599999999'
      ]]);
      $this->assertResponseStatusCodeSame(201);
    }

    public function testRegisterCustomer(): void
    {
      $response = static::createClient()->request('POST', '/api/auth/register/customer',
      ['json' => [
        'username' => 'dummycustomer',
        'password' => 'dummyPass1',
        'phone' => '13599999999',
        'email' => 'dummycustomer@example.loc'
      ]]);
      $this->assertResponseStatusCodeSame(201);
    }

    public function testTeamLeaderList(): void
    {
      $token = $this->getToken('vasea');
      $client = static::createClient([], [
        'auth_bearer' => $token,
        'headers' => [
          'accept' => 'application/ld+json'
      ]]);

      $response = $client->request('GET', '/api/users');
      $this->assertResponseStatusCodeSame(20  0);
    }
}
