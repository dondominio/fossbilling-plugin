<?php

namespace FOSSBilling\Registrar\DonDominio\Actions;

use Dondominio\API\API;
use Dondominio\API\Response\Response;

abstract class BaseAction
{
    public function __construct(protected API $api)
    {
    }

    protected function checkResponse(Response $response): void
    {
        if (!$response->getSuccess()) {
            // 1100 = Insufficient Balance
            if ($response->getErrorCode() == 1100) {
                throw new \Registrar_Exception('Error renewing domain. Please, try again later.');
            }

            $errorCodeMsg = $response->getErrorCodeMsg();
            $errorMessages = $response->getErrorMessages();
            $message = false ? $errorCodeMsg : sprintf('%s ( %s )', $errorCodeMsg, implode(', ', $errorMessages));

            throw new \Registrar_Exception($message);
        }
    }

    protected function getDomainName(\Registrar_Domain $domain): string
    {
        return $domain->getSld() . $domain->getTld();
    }

    protected function parseDomainContacts(\Registrar_Domain $domain): array
    {
        return [
            ...$this->parseContact($domain->getContactRegistrar(), 'owner'),
            ...$this->parseContact($domain->getContactAdmin(), 'admin'),
            ...$this->parseContact($domain->getContactTech(), 'tech'),
            ...$this->parseContact($domain->getContactBilling(), 'billing'),
        ];
    }

    private function parseContact(\Registrar_Domain_Contact $contact, string $type): array
    {
        $fields[$type.'ContactType'] = 'individual';
        $fields[$type.'ContactFirstName'] = $contact->getFirstName();
        $fields[$type.'ContactLastName'] = $contact->getLastName();
        $fields[$type.'ContactOrgName'] = $contact->getCompany();
        $fields[$type.'ContactEmail'] = $contact->getEmail();
        $fields[$type.'ContactAddress'] = $contact->getAddress();
        $fields[$type.'ContactCity'] = $contact->getCity();
        $fields[$type.'ContactState'] = $contact->getState();
        $fields[$type.'ContactCountry'] = $contact->getCountry();
        $fields[$type.'ContactPostalCode'] = $contact->getZip();
        $fields[$type.'ContactPhone'] = '+'.$contact->getTelCc().'.'.$contact->getTel();
        $fields[$type.'ContactIdentNumber'] = $contact->getDocumentNr();

        return $fields;
    }
}
