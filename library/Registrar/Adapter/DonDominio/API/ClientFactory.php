<?php

namespace FOSSBilling\Registrar\DonDominio\API;

use Dondominio\API\API;

class ClientFactory
{
    public static function instance(string $user, string $password)
    {
        return new API([
            'endpoint' => 'https://simple-api-test.dondominio.net',
            'apiuser' => $user,
            'apipasswd' => $password,
            'autoValidate' => false,
            'response' => [
                'throwExceptions' => false
            ]
        ]);
    }
}
