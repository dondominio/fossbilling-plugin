<?php

namespace FOSSBilling\Registrar\DonDominio\Actions;

class RegisterDomain extends BaseAction
{
    public function __invoke(\Registrar_Domain $domain, \Model_ClientOrder $order): bool
    {
        if (!(new IsDomainAvailable($this->api))($domain)) {
            throw new \Registrar_Exception('Domain already taken');
        }

        $nameservers = [$domain->getNs1(),$domain->getNs2(),$domain->getNs3(),$domain->getNs4()];
        $fields = [
            'nameservers' => implode(',', $nameservers),
            'period' => $domain->getRegistrationPeriod(),
            ...$this->parseDomainContacts($domain),
            ...$this->getAdditionalFields($domain, $order),
        ];

        $response = $this->api->domain_create($this->getDomainName($domain), $fields);
        $this->checkResponse($response);

        return true;
    }

    private function getAdditionalFields(\Registrar_Domain $domain, \Model_ClientOrder $order): array
    {
        $additionalFields = [];
        $keys = $this->getAdditionalFieldsKeys($domain);
        $config = json_decode($order->config, true);

        if (!is_array($config)) {
            return [];
        }

        foreach ($keys as $key) {
            if (array_key_exists($key, $config)) {
                $additionalFields[] = $config[$key];
            }
        }

        return $additionalFields;
    }

    private function getAdditionalFieldsKeys(\Registrar_Domain $domain): array
    {
        return match (strtolower($domain->getTld(false))) {
            'aero' => ['aeroId', 'aeroPass'],
            'barcelona', 'madrid', 'cat', 'scot', 'eus', 'gal', 'quebec', 'radio' => ['domainIntendedUse'],
            'ee', 'fi', 'my', 'moscow', 'vn', 'hk' => ['ownerDateOfBirth'],
            'fr', 're', 'yt', 'pm', 'wf', 'tf' => ['frTradeMark', 'frSirenNumber'],
            'jobs' => ['frTradeMark', 'frSirenNumber'],
            'ru' => ['ownerDateOfBirth', 'ruIssuer', 'ruIssuerDate'],
            'xxx' => ['xxxClass', 'xxxName', 'xxxEmail', 'xxxId'],
            default => [],
        };
    }
}
