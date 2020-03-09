<?php
// api/tests/UsersTest.php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\User;
// use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class UsersTest extends ApiTestCase
{
    // This trait provided by HautelookAliceBundle will take care of refreshing the database content to a known state before each test
    // use RefreshDatabaseTrait;

    private function testLogin(): void
    {
      dd('Test is runnning...');
        $response = static::createClient()->request('POST', '/login', ['json' => [
            'username' => 'admin',
            'password' => 'admin',
        ]]);

        $this->assertResponseIsSuccessfull();
        $this->assertArrayHasKey('token', $response->toArray());
    }
}
