<?php

namespace FOSSBilling\Registrar\DonDominio\Actions;

class GetTLDs extends BaseAction
{
    private const PAGE_LENGTH = 100;

    public function __invoke(): array
    {
        $tlds = [];
        $page = 0;

        do {
            $page++;
            $response = $this->api->account_zones([
                'pageLength' => static::PAGE_LENGTH,
                'page' => $page,
            ]);

            $total = $response->get('queryInfo')['total'] ?? 0;
            $responseTLDs = array_map(fn (array $info): string => $info['tld'] ?? '', $response->get('zones'));
            $tlds = array_merge($tlds, $responseTLDs);
        } while ($page * static::PAGE_LENGTH < $total);

        return $tlds;
    }
}
