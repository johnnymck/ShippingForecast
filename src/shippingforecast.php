<?php

namespace Johnnymck\ShippingForecast;

require_once 'vendor/autoload.php';

use Goutte\Client;

class ShippingForecast
{
    protected $client;
    // This address may update in the future, so will need to be amended
    protected $source = 'https://www.bbc.com/weather/coast_and_sea/shipping_forecast';
    protected $locations = [];
    protected $time;

    public function __construct()
    {
        $this->client = new Client();
        $this->load();
    }

    protected function load(): void
    {
        try {
            $crawler = $this->client->request('GET', $this->source);
            $this->time = $crawler->filter('h2.issued')->text();
            for ($i = 1; $i <= 31; $i++) {
                $area = $crawler->filter('#area-' . $i);
                $location = $area->filter('h2')->text();
                $warningDetail = $area->filter('.warning-detail');
                if (($warningDetail->count()) == 1) {
                    $warning = [
                        'title' => str_replace(':', '', $warningDetail->filter('strong')->text()),
                        'issued' => $warningDetail->filter('.issued')->text(),
                        'summary' => $warningDetail->filter('.summary')->text(),
                    ];
                } else {
                    $warning = [];
                }
                $breakdown = $area->filter('ul')->children()->filter('span');
                $location_report = [
                    'warning' => $warning,
                    'location' => $location,
                    'wind' => $breakdown->eq(0)->text(),
                    'seas' => $breakdown->eq(1)->text(),
                    'weather' => $breakdown->eq(2)->text(),
                    'visibility' => $breakdown->eq(3)->text(),
                ];
                $this->locations[$location] = $location_report;
            }
        } catch (Exception $e) {
            echo 'ERROR:\n';
            echo $e->getMessage();
        }
    }

    public function get($location): array
    {
        return [
            'time' => $this->time,
            'content' => $this->locations[$location],
        ];
    }

    public function getAll(): array
    {
        return [
            'time' => $this->time,
            'content' => $this->locations,
        ];
    }
}
