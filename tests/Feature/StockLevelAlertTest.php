<?php

namespace Tests\Feature;

use App\Models\StockItem;
use App\Services\StockLevelService;
use Tests\TestCase;

class StockLevelAlertTest extends TestCase
{
    public function test_stock_level_service_computes_3_2_1_month_parameters()
    {
        // 120 units annual consumption -> 10 units / month
        // 1 month min = 10
        // 2 months reorder = 20
        // 3 months max = 30
        $levels = StockLevelService::calculateParameters(120);

        $this->assertEquals(10, $levels['min']);
        $this->assertEquals(20, $levels['reorder']);
        $this->assertEquals(30, $levels['max']);
    }

    public function test_stock_level_status_alerts()
    {
        // Item with min 10, reorder 20, max 30
        $statusBelowMin = StockLevelService::determineStatus(5, 10, 20, 30);
        $this->assertEquals('BELOW_MIN', $statusBelowMin);

        $statusReorder = StockLevelService::determineStatus(15, 10, 20, 30);
        $this->assertEquals('REORDER', $statusReorder);

        $statusNormal = StockLevelService::determineStatus(25, 10, 20, 30);
        $this->assertEquals('NORMAL', $statusNormal);

        $statusAboveMax = StockLevelService::determineStatus(35, 10, 20, 30);
        $this->assertEquals('ABOVE_MAX', $statusAboveMax);
    }
}
