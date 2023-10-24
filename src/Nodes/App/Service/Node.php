<?php
declare(strict_types=1);
namespace PromoxApiClient\Nodes\App\Service;

use PromoxApiClient\Commons\Application\Helpers\GFunctions;
use PromoxApiClient\Commons\Domain\Entities\Connection;
use PromoxApiClient\Commons\Domain\Entities\CookiesPVE;
use PromoxApiClient\Commons\infrastructure\GClientBase;

final class Node extends GClientBase
{
    use GFunctions;



    public function __construct(Connection $connection, CookiesPVE $cookiesPVE)
    {
        parent::__construct($connection, $cookiesPVE);
    }


    public function __invoke(): array
    {
        $result = $this->Get("/nodes",[]);
        var_dump("Llamada nodes ->" . json_encode($result));
        return $result;
    }
}