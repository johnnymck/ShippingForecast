<?php

namespace ShippingForecast;

require_once 'vendor/autoload.php';

use Goutte\Client;

/**
 * Scrapes data from BBC shipping forecast webpage and returns it in an easy to use format.
 */
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

    /**
     * Performs initialisation upon instantiation by getting scraping from BBC webpage.
     *
     * Not _strictly_ savvy to format the initialisation this way, but may prove more DRY in future.
     */
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

    /**
     * Takes a specific location and returns forecast on that location.
     *
     * @param string $location Capitalised forecast zone eg, 'Forth', 'German Bight' or
     * 'Dogger' as opposed to 'forth', 'german bight' or 'dogger'.
     *
     * @return array Returns an assoc arrray of the data, with a time key and a content key.
     */
    public function get($location): array
    {
        return [
            'time' => $this->time,
            'content' => $this->locations[$location],
        ];
    }

    /**
     * Returns an array of all locations and their respective forecast and warnings.
     *
     * @return array Returns an array of all locations containing the data, with a time key and a content key as with ShippingForecast::get().
     */
    public function getAll(): array
    {
        return [
            'time' => $this->time,
            'content' => $this->locations,
        ];
    }
}
