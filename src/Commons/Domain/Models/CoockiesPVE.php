<?php
declare(strict_types=1);
namespace PromoxApiClient\Commons\Domain\Models;

use GuzzleHttp\Cookie\CookieJar;

Interface CoockiesPVE
{


    /**
     * @param string $CSRFPreventionToken
     * @param CookieJar $cookies
     * @param string $ticket
     */
    public function __construct(string $CSRFPreventionToken, CookieJar $cookies, string $ticket);



    public function getCSRFPreventionToken(): string;

    public function getCookies(): CookieJar;

    public function getTicket(): string;

}