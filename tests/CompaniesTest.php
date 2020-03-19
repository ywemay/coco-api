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

  const IRI = '/api/companies';
  const COMPANY = 'Company Name LTD';

  public function setUp(){
    $this->setIri(self::IRI);
  }

  public function testList(): void
  {
      $this->userRequest(false, self::IRI);
      $this->assertResponseStatusCodeSame(401);

      // admin
      $this->userRequest('admin', self::IRI);
      $this->assertResponseStatusCodeSame(200);

      // customer request
      $this->userRequest('customer', self::IRI);
      $this->assertResponseStatusCodeSame(403);

      $this->userRequest('teamleader', self::IRI);
      $this->assertResponseStatusCodeSame(403);

      $this->userRequest('worker', self::IRI);
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
    print "Try viewing \"customer\"'s company as $username... ";
    $client = $this->getAuthenticatedClient($username);
    $useriri = self::findIriBy(User::class, ['username' => 'customer']);
    $iri = self::findIriBy(Company::class, ["owner" => $useriri]);
    $response = $client->request('GET', $iri);
    print $response->getStatusCode() . "\n";
    return $response;
  }

  private function multitest($params)
  {
    $params += array(
      'context' => 'Unknown',
      'iri' => $this->getIri(),
      'method' => 'GET',
      'params' => [],
      'users' => []
    );

    $o = (object) $params;

    print "\n";
    if ($o->users) {
      foreach ($o->users as $key => $code) {
        $f = $key . 'Request';
        $resp = $this->$f($o->method, $o->iri, $o->params);
        $this->assertResponseStatusCodeSame($code, "Failed to get asseted $code for $key in $o->context");
      }
    }
  }

  public function testCreate(): void
  {
    // $r = $this->createCompany('admin');
    // $this->assertResponseStatusCodeSame(201);
    // $this->assertSame($r->toArray()['name'], self::COMPANY);

    // $r = $this->createCompany('customer');
    // $this->assertResponseStatusCodeSame(500);

    $client = $this->getAuthenticatedClient('admin');

    $json = ['name' => self::COMPANY];
    $json['owner'] = self::findIriBy(User::class, ['username' => 'customer']);
    $params['json'] = $json;

    $this->multitest(array(
      'context' => 'testCreate',
      'method' => 'POST',
      'params' => $params,
      'users' => array(
        'admin' => 201,
        'anonymous' => 401,
        'teamleader' => 403,
        'customer' => 400,
      )
    ));

/*
    $this->adminRequest('POST', $this->getIri(), $params);
    $this->assertResponseStatusCodeSame(200);

    $this->adminRequest('POST', $this->getIri(), $params);
    $this->assertResponseStatusCodeSame(200);

    $client = static::createClient([], [
      'auth_bearer' => $this->getToken('admin'),
      'headers' => [
        'accept' => 'application/ld+json'
    ]]);
    $json = ['name' => self::COMPANY];
    $json['owner'] = self::findIriBy(User::class, ['username' => 'customer2']);
    $response = $client->request('POST', self::IRI, ['json' => $json]);

    $response = $client->request('GET', self::IRI, [
      'query' => [
        // 'name' => 'Company'
        'owner' => 35// $json['owner']
      ],
    ]);
    dd($response->toArray());
    $this->assertResponseStatusCodeSame(201);*/
  }

  public function testCreate2(): void
  {
    $r = $this->createCompany('customer2');
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
    return $client->request('POST', self::IRI, ['json' => $json]);
  }
}
