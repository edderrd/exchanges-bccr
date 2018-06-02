<?php

namespace ExchangesBCCR\Tests;

use ExchangesBCCR\Scraper;
use PHPUnit\Framework\TestCase;

class ScraperTest extends TestCase
{
    /**
     * @var Scraper
     */
    protected $scraper;

    /**
     * Parsed results from the scraper
     *
     * @var array
     */
    protected $results;

    /**
     * Initialization
     */
    public function setUp()
    {
        $this->scraper = new Scraper();
        $this->results = $this->scraper->fetch();
    }

    /** @test */
    public function it_test_scraper_connects_to_endpoint()
    {
        $this->assertEquals($this->scraper->status(), 200);
    }

    /** @test */
    public function it_test_scraper_results_have_data()
    {
        dd($this->results);
        $this->assertTrue(count($this->results) > 10);
        $this->assertArrayHasKey('buy', $this->results[0]);
        $this->assertArrayHasKey('sell', $this->results[0]);
        $this->assertArrayHasKey('bank', $this->results[0]);
        $this->assertArrayHasKey('updated_at', $this->results[0]);
    }
}
