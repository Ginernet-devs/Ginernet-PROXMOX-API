<?php
declare(strict_types=1);
namespace PromoxApiClient\Auth\App\Service;

use GuzzleHttp\Exception\GuzzleException;
use PromoxApiClient\Auth\Domain\Responses\LoginResponse;
use PromoxApiClient\Commons\Application\Helpers\GFunctions;
use PromoxApiClient\Commons\Domain\Exceptions\AuthFailedException;
use PromoxApiClient\Commons\Domain\Exceptions\HostUnreachableException;
use PromoxApiClient\Commons\Domain\Models\Connection;
use PromoxApiClient\Commons\infrastructure\GClientBase;

final class Login extends GClientBase
{
    use GFunctions;

    private string $ticket;

    private array $defaultHeaders = [
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
    ];

    public function __construct(Connection $connection)
    {
         parent::__construct($connection);
    }

    public function __invoke(): ?LoginResponse

    {
        try {
            $body=[
                'username' => $this->getConnection()->getUsername(),
                'password' => $this->getConnection()->getPassword(),
                'realm' => $this->getConnection()->getRealm()
            ];

            $result=  $this->getClient()->request("POST", $this->getConnection()->getUri() .'access/ticket' , [
                'https_errors'=>false,
                'verify' => false,
                'headers' => $this->defaultHeaders,
                'json' => (count($body) > 0 ) ? $body : null]);
           $response = $this->decodeBody($result);
           $cookie = $this->getCookies($response['ticket'], $this->GetConnection()->getHost());
           return new LoginResponse($response['CSRFPreventionToken'], $cookie, $response['ticket']);
        } catch (GuzzleException $ex) {
            if ($ex->getCode() === 401) throw new AuthFailedException();
            if ($ex->getCode() === 0) throw new HostUnreachableException();
        }
        return null;
    }


}
