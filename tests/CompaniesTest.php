<?php
// api/tests/BooksTest.php

namespace App\Tests;

use App\Tests\ApiJWTTestCase;
use App\Entity\Company;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class BooksTest extends ApiJWTTestCase
{
  // This trait provided by HautelookAliceBundle will take care of refreshing the database content to a known state before each test
  use RefreshDatabaseTrait;

  const IRI = '/api/companies';

  public function testList(): void
  {
      // anonymous request
      $client = static::createClient();
      $response = $client->request('GET', self::IRI);
      $this->assertSame(401, $response->getStatusCode());

      // admin
      $response = $this->userRequest('admin', self::IRI);
      $this->assertResponseStatusCodeSame(200);

      // customer request
      $response = $this->userRequest('orange', self::IRI);
      $this->assertResponseStatusCodeSame(200);

      $response = $this->userRequest('vasea', self::IRI);
      $this->assertResponseStatusCodeSame(403);

      $response = $this->userRequest('pekya', self::IRI);
      $this->assertResponseStatusCodeSame(403);
  }
}
