<?php

namespace ExchangesBCCR;

use Carbon\Carbon;
use Goutte\Client;

class Scraper
{
    /**
     * BCCR Exchanges URL
     *
     * @var string
     */
    protected $url = 'http://indicadoreseconomicos.bccr.fi.cr/IndicadoresEconomicos/Cuadros/frmConsultaTCVentanilla.aspx';

    /**
     * Scraper Client
     *
     * @var Client
     */
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function fetch()
    {
        $crawler = $this->request();
        $results = [];
        try {
            $results = $crawler->filter('table#Table2 table[rules=all] tr[align=right]')->each(function ($node) {
                return [
                    'bank' => trim($node->children()->eq(1)->text()),
                    'buy' => str_replace(',', '.', trim($node->children()->eq(2)->text())),
                    'sell' => str_replace(',', '.', trim($node->children()->eq(3)->text())),
                    'updated_at' => Carbon::createFromFormat('d/m/Y H:i a', str_replace('    ', ' ', trim($node->children()->eq(5)->text()))),
                ];
            });
        } catch (Exception $e) {
            throw new ScraperException(__CLASS__ . ': ' . $e->getMessage());
        }
        return $results;
    }

    /**
     * Performs a request to the BCCR page to be scraped
     *
     * @throws ScraperException
     * @return Crawler
     */
    public function request()
    {
        $crawler = $this->client->request('GET', $this->url);

        switch ($this->client->getResponse()->getStatus()) {
            case 200:
                return $crawler;
            case 404:
                throw new ScraperException("Page cannot be found: ($this->url)", 404);
            case 500:
                throw new ScraperException("Page has internal errors, cannot be accessed {$this->client->getResponse()->getContent()}");
            default:
                throw new ScraperException($this->client->getResponse()->getContent());
        }

        return $crawler;
    }

    /**
     * Return response status
     * request method must be called first
     * @return int
     */
    public function status()
    {
        return $this->client->getResponse()->getStatus();
    }
}
