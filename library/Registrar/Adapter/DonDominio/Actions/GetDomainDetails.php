<?php

namespace FOSSBilling\Registrar\DonDominio\Actions;

class GetDomainDetails extends BaseAction
{
    public function __invoke(\Registrar_Domain $domain): \Registrar_Domain
    {
        $this->setStatus($domain);
        $this->setAuthCode($domain);
        $this->setNameServers($domain);
        $this->setContacts($domain);

        return $domain;
    }

    private function setStatus(\Registrar_Domain $domain): void
    {
        $response = $this->api->domain_getInfo($this->getDomainName($domain), ['infoType' => 'status']);
        $this->checkResponse($response);

        $domain->setPrivacyEnabled($response->get('whoisPrivacy'));
        $domain->setLocked($response->get('modifyBlock'));
    }

    private function setAuthCode(\Registrar_Domain $domain): void
    {
        $response = $this->api->domain_getInfo($this->getDomainName($domain), ['infoType' => 'authcode']);
        $this->checkResponse($response);

        $domain->setEpp($response->get('authcode'));
    }

    private function setNameServers(\Registrar_Domain $domain): void
    {
        $response = $this->api->domain_getInfo($this->getDomainName($domain), ['infoType' => 'nameservers']);
        $this->checkResponse($response);

        $nameServers = $response->get('nameservers');
        $nameServers = is_array($nameServers) ? $nameServers : [];
        $domain->setNs1($nameServers[0]['name'] ?? '');
        $domain->setNs2($nameServers[1]['name'] ?? '');
        $domain->setNs3($nameServers[2]['name'] ?? '');
        $domain->setNs4($nameServers[3]['name'] ?? '');
    }

    private function setContacts(\Registrar_Domain $domain): void
    {
        $response = $this->api->domain_getInfo($this->getDomainName($domain), ['infoType' => 'contact']);
        $this->checkResponse($response);

        $contactOwner = $response->get('contactOwner');
        $domain->setContactRegistrar($this->parseAPIContact($contactOwner, $domain->getContactRegistrar()));

        $contactAdmin = $response->get('contactAdmin');
        $domain->setContactRegistrar($this->parseAPIContact($contactAdmin, $domain->getContactAdmin()));

        $contactTech = $response->get('contactTech');
        $domain->setContactRegistrar($this->parseAPIContact($contactTech, $domain->getContactTech()));

        $contactBilling = $response->get('contactBilling');
        $domain->setContactRegistrar($this->parseAPIContact($contactBilling, $domain->getContactBilling()));
    }

    private function parseAPIContact(array $apiContact, \Registrar_Domain_Contact $contact): \Registrar_Domain_Contact
    {
        $contact->setFirstName($apiContact['firstName']);
        $contact->setLastName($apiContact['lastName']);
        $contact->setCompany($apiContact['orgName']);
        $contact->setEmail($apiContact['email']);
        $contact->setAddress1($apiContact['address']);
        $contact->setCity($apiContact['city']);
        $contact->setState($apiContact['state']);
        $contact->setCountry($apiContact['country']);
        $contact->setZip($apiContact['postalCode']);

        $phone = $apiContact['phone'];
        if (preg_match('/^\+([^\.]*)\.(.*)/', $phone, $matches)) {
            $contact->setTelCc($matches[1]);
            $contact->setTel($matches[2]);
        }

        return $contact;
    }
}
