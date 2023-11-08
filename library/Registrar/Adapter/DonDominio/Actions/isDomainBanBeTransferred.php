<?php

namespace FOSSBilling\Registrar\DonDominio\Actions;

class isDomainBanBeTransferred extends BaseAction
{
    public function __invoke(\Registrar_Domain $domain): bool
    {
        $response = $this->api->domain_checkForTransfer($this->getDomainName($domain));
        $this->checkResponse($response);
        $domainInfo = $response->get('domains')[0];

        if ($domainInfo['transferavail']) {
            return true;
        }

        if (!empty($domainInfo['transfermsg'])) {
            throw new \Registrar_Exception(implode(', ', $domainInfo['transfermsg']));
        }

        return false;
    }
}
