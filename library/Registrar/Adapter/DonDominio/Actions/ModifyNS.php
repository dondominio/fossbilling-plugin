<?php

namespace FOSSBilling\Registrar\DonDominio\Actions;

class ModifyNS extends BaseAction
{
    public function __invoke(\Registrar_Domain $domain): bool
    {
        $response = $this->api->domain_updateNameServers($this->getDomainName($domain), [
            $domain->getNs1(),
            $domain->getNs2(),
            $domain->getNs3(),
            $domain->getNs4(),
        ]);
        $this->checkResponse($response);

        return true;
    }
}
