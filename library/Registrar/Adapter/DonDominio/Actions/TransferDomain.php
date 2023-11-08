<?php

namespace FOSSBilling\Registrar\DonDominio\Actions;

class TransferDomain extends BaseAction
{
    public function __invoke(\Registrar_Domain $domain): bool
    {
        $fields = [
            'ns1' => $domain->getNs1(),
            'ns2' => $domain->getNs2(),
            'ns3' => $domain->getNs3(),
            'ns4' => $domain->getNs4(),
        ];
        $fields = array_merge($fields, $this->parseDomainContacts($domain));
        $fields['authcode'] = $domain->getEpp();

        $response = $this->api->domain_transfer($domain, $fields);
        if (!$response->getSuccess()) {
            throw new \Registrar_Exception('Error transfering domain. Please, try again later.');
        }

        $this->checkResponse($response);

        return true;
    }
}
