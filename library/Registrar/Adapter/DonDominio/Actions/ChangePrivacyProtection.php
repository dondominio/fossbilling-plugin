<?php

namespace FOSSBilling\Registrar\DonDominio\Actions;

class ChangePrivacyProtection extends BaseAction
{
    public function __invoke(\Registrar_Domain $domain, bool $status): bool
    {
        $fields = [
            'updateType' => 'whoisPrivacy',
            'whoisPrivacy' => $status
        ];

        $response = $this->api->domain_update($this->getDomainName($domain), $fields);
        $this->checkResponse($response);

        return true;
    }
}
