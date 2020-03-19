<?php
// api/tests/BooksTest.php

namespace App\Tests;

use App\Tests\ApiJWTTestCase;
use App\Entity\Company;
use App\Entity\User;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class BooksTest extends ApiJWTTestCase
{
  // This trait provided by HautelookAliceBundle will take care of refreshing the database content to a known state before each test
  use RefreshDatabaseTrait;

  const COMPANY = 'Company Name LTD';

  public function setUp(){
    $this->setIri('/api/companies');
  }

  public function testList(): void
  {

      $this->anonymousRequest();
      $this->assertResponseStatusCodeSame(401);

      $this->adminRequest();
      $this->assertResponseStatusCodeSame(200);

      $this->customerRequest();
      $this->assertResponseStatusCodeSame(403);

      $this->teamleaderRequest();
      $this->assertResponseStatusCodeSame(403);

      $this->workerRequest();
      $this->assertResponseStatusCodeSame(403);
  }

  public function testView(): void
  {
    print "\n";
    $this->viewCompany('customer');
    $this->assertResponseStatusCodeSame(200);
    $this->viewCompany('customer2');
    $this->assertResponseStatusCodeSame(403);
    $this->viewCompany('admin');
    $this->assertResponseStatusCodeSame(200);
    $this->viewCompany('teamleader');
    $this->assertResponseStatusCodeSame(403);
    $this->viewCompany('worker');
    $this->assertResponseStatusCodeSame(403);
  }

  private function viewCompany($username)
  {
    $client = $this->getAuthenticatedClient($username);
    $useriri = self::findIriBy(User::class, ['username' => 'customer']);
    $iri = self::findIriBy(Company::class, ["owner" => $useriri]);
    $response = $client->request('GET', $iri);
    return $response;
  }

  public function testCreate(): void
  {
    $client = $this->getAuthenticatedClient('admin');

    $json = ['name' => self::COMPANY];
    $json['owner'] = self::findIriBy(User::class, ['username' => 'customer2']);
    $params['json'] = $json;

    $this->anonymousRequest('POST', $this->getIri(), $params);
    $this->assertResponseStatusCodeSame(401);

    $r = $this->adminRequest('POST', $this->getIri(), $params);
    $this->assertResponseStatusCodeSame(201);
    $this->assertSame(self::COMPANY, $r->toArray()['name']);

    $this->teamleaderRequest('POST', $this->getIri(), $params);
    $this->assertResponseStatusCodeSame(403);

    $r = $this->customerRequest('POST', $this->getIri(), $params);
    $this->assertResponseStatusCodeSame(400);
  }

  public function testCreate2(): void
  {
    $r = $this->createCompany('customer3');
    $this->assertResponseStatusCodeSame(201);
    $this->assertSame($r->toArray()['name'], self::COMPANY);
  }


  private function createCompany($username)
  {
    $client = $this->getAuthenticatedClient($username);
    $json = ['name' => self::COMPANY];
    if ($username == 'admin') {
      $json['owner'] = self::findIriBy(User::class, ['username' => 'customer2']);
    }
    return $client->request('POST', $this->getIri(), ['json' => $json]);
  }

  public function testDelete() {

    $client = $this->getAuthenticatedClient('admin');

    $uid = self::findIriBy(User::class, ['username' => 'customer']);
    $iri = $this->findIriBy(Company::class, ['owner' => $uid]);

    $this->anonymousRequest('DELETE', $iri);
    $this->assertResponseStatusCodeSame(401);

    $this->workerRequest('DELETE', $iri);
    $this->assertResponseStatusCodeSame(403);

    $this->teamleaderRequest('DELETE', $iri);
    $this->assertResponseStatusCodeSame(403);

    $this->customerRequest('DELETE', $iri);
    $this->assertResponseStatusCodeSame(403);

    $this->adminRequest('DELETE', $iri);
    $this->assertResponseStatusCodeSame(403);
  }
}
