<?php

require_once __DIR__ . '/DonDominio/autoloader.php';

use Dondominio\API\API;
use FOSSBilling\Registrar\DonDominio\Actions\ChangeLock;
use FOSSBilling\Registrar\DonDominio\Actions\ChangePrivacyProtection;
use FOSSBilling\Registrar\DonDominio\Actions\GetDomainDetails;
use FOSSBilling\Registrar\DonDominio\Actions\GetEPP;
use FOSSBilling\Registrar\DonDominio\Actions\GetTLDs;
use FOSSBilling\Registrar\DonDominio\Actions\IsDomainAvailable;
use FOSSBilling\Registrar\DonDominio\Actions\isDomainBanBeTransferred;
use FOSSBilling\Registrar\DonDominio\Actions\ModifyContact;
use FOSSBilling\Registrar\DonDominio\Actions\ModifyNS;
use FOSSBilling\Registrar\DonDominio\Actions\RegisterDomain;
use FOSSBilling\Registrar\DonDominio\Actions\RenewDomain;
use FOSSBilling\Registrar\DonDominio\Actions\TransferDomain;
use FOSSBilling\Registrar\DonDominio\API\ClientFactory;

class Registrar_Adapter_DonDominio extends Registrar_AdapterAbstract
{
    private string $username;
    private string $password;
    private string $override_owner = '';
    private string $override_admin = '';
    private string $override_tech = '';
    private string $override_billing = '';

    public function __construct($options)
    {
        if (isset($options['Username']) && !empty($options['Username'])) {
            $this->username = $options['Username'];
        } else {
            throw new Registrar_Exception('No Username');
        }

        if (isset($options['Password']) && !empty($options['Password'])) {
            $this->password = $options['Password'];
        } else {
            throw new Registrar_Exception('No Password');
        }

        $this->override_owner = $options['override_owner'] ?? '';
        $this->override_admin = $options['override_admin'] ?? '';
        $this->override_tech = $options['override_tech'] ?? '';
        $this->override_billing = $options['override_billing'] ?? '';
    }

    public static function getConfig(): array
    {
        return [
            'label' => 'DonDominio',
            'form'  => [
                'Username' => [
                    'text',
                    [
                        'label' => 'API Username',
                        'description'=> '',
                        'required' => true,
                    ],
                ],
                'Password' => [
                    'password',
                    [
                        'label' => 'API Password',
                        'description'=> '',
                        'required' => true,
                    ],
                ],

                //Override contact information
                'override_owner' => [
                    'text',
                    [
                        'label' => 'OVERRIDE OWNER CONTACT INFORMATION',
                        'description' => '
							Enter a DonDominio Contact ID to use when registering or transferring
							domains instead of the customer contact information. A DonDominio
							Contact ID looks like this: AAA-00000. Find it in your domain details
							page on the DonDominio website.
						',
                        'required' => false
                    ]
                ],
                'override_admin' => [
                    'text',
                    [
                        'label' => 'OVERRIDE ADMIN CONTACT INFORMATION',
                        'description' => '
							Enter a DonDominio Contact ID to use when registering or transferring
							domains instead of the customer contact information. A DonDominio
							Contact ID looks like this: AAA-00000. Find it in your domain details
							page on the DonDominio website.
						',
                        'required' => false
                    ]
                ],

                'override_tech' => [
                    'text',
                    [
                        'label' => 'OVERRIDE TECH CONTACT INFORMATION',
                        'description' => '
							Enter a DonDominio Contact ID to use when registering or transferring
							domains instead of the customer contact information. A DonDominio
							Contact ID looks like this: AAA-00000. Find it in your domain details
							page on the DonDominio website.
						',
                        'required' => false
                    ]
                ],

                'override_billing' => [
                    'text',
                    [
                        'label' => 'OVERRIDE BILLING CONTACT INFORMATION',
                        'description' => '
							Enter a DonDominio Contact ID to use when registering or transferring
							domains instead of the customer contact information. A DonDominio
							Contact ID looks like this: AAA-00000. Find it in your domain details
							page on the DonDominio website.
						',
                        'required' => false
                    ]
                ]
            ],
        ];
    }

    public function getTlds(): array
    {
        return (new GetTLDs(
            $this->getDDAPI(),
            $this->override_owner,
            $this->override_admin,
            $this->override_tech,
            $this->override_billing,
        ))();
    }

    public function isDomainAvailable(Registrar_Domain $domain): bool
    {
        return (new IsDomainAvailable(
            $this->getDDAPI(),
            $this->override_owner,
            $this->override_admin,
            $this->override_tech,
            $this->override_billing,
        ))($domain);
    }

    public function isDomaincanBeTransferred(Registrar_Domain $domain): bool
    {
        return (new isDomainBanBeTransferred(
            $this->getDDAPI(),
            $this->override_owner,
            $this->override_admin,
            $this->override_tech,
            $this->override_billing,
        ))($domain);
    }

    public function modifyNs(Registrar_Domain $domain): bool
    {
        return (new ModifyNS(
            $this->getDDAPI(),
            $this->override_owner,
            $this->override_admin,
            $this->override_tech,
            $this->override_billing,
        ))($domain);
    }

    public function modifyContact(Registrar_Domain $domain): bool
    {
        return (new ModifyContact(
            $this->getDDAPI(),
            $this->override_owner,
            $this->override_admin,
            $this->override_tech,
            $this->override_billing,
        ))($domain);
    }

    public function transferDomain(Registrar_Domain $domain): bool
    {
        return (new TransferDomain(
            $this->getDDAPI(),
            $this->override_owner,
            $this->override_admin,
            $this->override_tech,
            $this->override_billing,
        ))($domain);
    }

    public function getDomainDetails(Registrar_Domain $domain): Registrar_Domain
    {
        return (new GetDomainDetails(
            $this->getDDAPI(),
            $this->override_owner,
            $this->override_admin,
            $this->override_tech,
            $this->override_billing,
        ))($domain);
    }

    public function getEpp(Registrar_Domain $domain): string
    {
        return (new GetEPP(
            $this->getDDAPI(),
            $this->override_owner,
            $this->override_admin,
            $this->override_tech,
            $this->override_billing,
        ))($domain);
    }

    public function registerDomain(Registrar_Domain $domain): bool
    {
        if (!$this->_order) {
            throw new Registrar_Exception('Order not found');
        }

        return (new RegisterDomain(
            $this->getDDAPI(),
            $this->override_owner,
            $this->override_admin,
            $this->override_tech,
            $this->override_billing,
        ))($domain, $this->_order);
    }

    public function renewDomain(Registrar_Domain $domain): bool
    {
        return (new RenewDomain(
            $this->getDDAPI(),
            $this->override_owner,
            $this->override_admin,
            $this->override_tech,
            $this->override_billing,
        ))($domain);
    }

    public function deleteDomain(Registrar_Domain $domain): bool
    {
        // throw new Registrar_Exception('Domain cannot be deleted');
        return true;
    }

    public function enablePrivacyProtection(Registrar_Domain $domain): bool
    {
        return (new ChangePrivacyProtection(
            $this->getDDAPI(),
            $this->override_owner,
            $this->override_admin,
            $this->override_tech,
            $this->override_billing,
        ))($domain, true);
    }

    public function disablePrivacyProtection(Registrar_Domain $domain): bool
    {
        return (new ChangePrivacyProtection(
            $this->getDDAPI(),
            $this->override_owner,
            $this->override_admin,
            $this->override_tech,
            $this->override_billing,
        ))($domain, false);
    }

    public function lock(Registrar_Domain $domain): bool
    {
        return (new ChangeLock(
            $this->getDDAPI(),
            $this->override_owner,
            $this->override_admin,
            $this->override_tech,
            $this->override_billing,
        ))($domain, true);
    }

    public function unlock(Registrar_Domain $domain): bool
    {
        return (new ChangeLock(
            $this->getDDAPI(),
            $this->override_owner,
            $this->override_admin,
            $this->override_tech,
            $this->override_billing,
        ))($domain, false);
    }

    private function getDDAPI(): API
    {
        return ClientFactory::instance($this->username, $this->password);
    }
}
