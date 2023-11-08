<?php

namespace FOSSBilling\Registrar\DonDominio\Actions;

class IsDomainAvailable extends BaseAction
{
    public function __invoke(\Registrar_Domain $domain): bool
    {
        $response = $this->api->domain_check($this->getDomainName($domain));
        $this->checkResponse($response);
        $domainInfo = $response->get('domains')[0];

        if (!$domainInfo['available']) {
            return false;
        }

        if ($domainInfo['premium']) {
            throw new \Registrar_Exception('Premium domains cannot be registered.');
        }

        return true;
    }
}
