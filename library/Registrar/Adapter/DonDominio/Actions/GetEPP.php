<?php

namespace FOSSBilling\Registrar\DonDominio\Actions;

class GetEPP extends BaseAction
{
    public function __invoke(\Registrar_Domain $domain): string
    {
        $response = $this->api->domain_getAuthCode($this->getDomainName($domain));
        $this->checkResponse($response);

        return $response->get('authcode');
    }
}
