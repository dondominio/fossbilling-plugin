<?php

namespace FOSSBilling\Registrar\DonDominio\Actions;

class RenewDomain extends BaseAction
{
    public function __invoke(\Registrar_Domain $domain): bool
    {
        $response = $this->api->domain_getInfo($this->getDomainName($domain), ['infoType' => 'status']);
        $this->checkResponse($response);

        $fields = [
            'curExpDate' => $response->get('tsExpir'),
            'period' => $domain->getRegistrationPeriod()
        ];

        $response = $this->api->domain_renew($this->getDomainName($domain), $fields);
        $this->checkResponse($response);

        return true;
    }
}
