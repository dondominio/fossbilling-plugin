<?php

namespace FOSSBilling\Registrar\DonDominio\Actions;

class ModifyContact extends BaseAction
{
    public function __invoke(\Registrar_Domain $domain): bool
    {
        $response = $this->api->domain_updateContacts($this->getDomainName($domain), $this->parseDomainContacts($domain));
        $this->checkResponse($response);

        return true;
    }
}
