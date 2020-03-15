<?php
namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
// use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class ApiJWTTestCase extends ApiTestCase
{
    // This trait provided by HautelookAliceBundle will take care of refreshing the database content to a known state before each test
    use RefreshDatabaseTrait;

    private $credentials = array(
      'username' => 'admin',
      'password' => 'admin'
    );

    public function getToken($credentials = []): string
    {
      if (is_string($credentials)) {
        $credentials = [
          'username' => $credentials,
          'password' => $credentials
        ];
      }
      if (!$credentials) $credentials = $this->credentials;
      $response = static::createClient()->request('POST', '/api/auth/login',
      ['json' => $credentials ]);

      if($response->getStatusCode() == 200) {
        $arr = $response->toArray();
        if (!isset($arr['token'])) return FALSE;
        return $arr['token'];
      }

      return FALSE;
    }

    public function getAuthenticatedClient($credentials = []) {
      $token = $this->getToken($credentials);

      $client = static::createClient([], [
        'auth_bearer' => $token,
        'headers' => [
          'accept' => 'application/ld+json'
      ]]);

      return $client;
    }

    public function userRequest($username, $iri, $method='GET')
    {
      $client = $username
        ? $this->getAuthenticatedClient($username)
        : static::createClient();
      return $client->request($method, $iri);
    }

    public function iriToId($iri)
    {
      $parts = explode("/", $iri);
      return end($parts);
    }

    public function findIriBy(string $resourceClass, array $criteria): ?string
    {
      foreach($criteria as $k => $v) {
        if (preg_match("/^\/api\/.*?(\d+)$/", $v, $mt)){
          $criteria[$k] = $mt[1];
        }
      }
      return parent::findIriBy($resourceClass, $criteria);
    }
}
