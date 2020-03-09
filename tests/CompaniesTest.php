<?php
// api/tests/BooksTest.php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Company;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class BooksTest extends ApiTestCase
{
  // This trait provided by HautelookAliceBundle will take care of refreshing the database content to a known state before each test
  use RefreshDatabaseTrait;

  public function testGetCompanies(): void
  {
  }
}
