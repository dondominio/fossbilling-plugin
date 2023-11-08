<?php

namespace FOSSBilling\Registrar\DonDominio\Actions;

class ChangeLock extends BaseAction
{
    public function __invoke(\Registrar_Domain $domain, bool $status): bool
    {
        $fields = [
            'updateType' => 'block',
            'block' => $status
        ];

        $response = $this->api->domain_update($this->getDomainName($domain), $fields);
        $this->checkResponse($response);

        $fields = [
            'updateType' => 'transferBlock',
            'transferBlock' => $status
        ];

        $response = $this->api->domain_update($this->getDomainName($domain), $fields);
        $this->checkResponse($response);

        return true;
    }
}
