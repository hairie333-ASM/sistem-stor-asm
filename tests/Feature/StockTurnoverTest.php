<?php

namespace Tests\Feature;

use App\Services\StockTurnoverService;
use Tests\TestCase;

class StockTurnoverTest extends TestCase
{
    public function test_kew_ps_14_turnover_calculation_structure()
    {
        $year = (int) date('Y');
        $report = StockTurnoverService::calculateQuarterlyReport($year);

        $this->assertArrayHasKey('year', $report);
        $this->assertArrayHasKey('quarters', $report);
        $this->assertCount(4, $report['quarters']);
        $this->assertArrayHasKey('turnover_rate', $report);
        $this->assertArrayHasKey('target_achieved', $report);
        $this->assertEquals(4.0, $report['target']);
    }
}
