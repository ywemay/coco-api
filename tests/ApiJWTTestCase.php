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
}
