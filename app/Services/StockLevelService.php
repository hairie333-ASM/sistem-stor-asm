<?php

namespace App\Services;

use App\Models\StockItem;
use App\Models\StockTransactionItem;
use Carbon\Carbon;

class StockLevelService
{
    /**
     * Recalculate 3-2-1 month stock parameters based on historical 12-month usage
     * Maximum = 3 months usage
     * Reorder = 2 months usage
     * Minimum = 1 month usage
     */
    public static function recalculateItemLevels(StockItem $stockItem): array
    {
        $twelveMonthsAgo = Carbon::now()->subMonths(12);

        $totalIssuedLast12Months = StockTransactionItem::where('stock_item_id', $stockItem->id)
            ->where('movement_type', 'OUT')
            ->whereHas('transaction', function ($q) use ($twelveMonthsAgo) {
                $q->whereIn('transaction_type', ['ISSUE', 'TRANSFER_OUT'])
                  ->where('transaction_date', '>=', $twelveMonthsAgo);
            })
            ->sum('quantity');

        $averageMonthlyUsage = $totalIssuedLast12Months > 0 ? ($totalIssuedLast12Months / 12) : 0;

        if ($averageMonthlyUsage > 0) {
            $minLevel = ceil($averageMonthlyUsage * 1);      // 1 month
            $reorderLevel = ceil($averageMonthlyUsage * 2);  // 2 months
            $maxLevel = ceil($averageMonthlyUsage * 3);      // 3 months

            $stockItem->min_level = $minLevel;
            $stockItem->reorder_level = $reorderLevel;
            $stockItem->max_level = $maxLevel;
            $stockItem->save();

            return [
                'monthly_usage' => round($averageMonthlyUsage, 2),
                'min_level' => $minLevel,
                'reorder_level' => $reorderLevel,
                'max_level' => $maxLevel,
            ];
        }

        return [
            'monthly_usage' => 0,
            'min_level' => (float) $stockItem->min_level,
            'reorder_level' => (float) $stockItem->reorder_level,
            'max_level' => (float) $stockItem->max_level,
        ];
    }

    /**
     * Compute 3-2-1 month parameters directly from annual consumption
     */
    public static function calculateParameters(float|int $annualUsage): array
    {
        $monthlyUsage = $annualUsage > 0 ? ($annualUsage / 12) : 0;
        return [
            'monthly_usage' => round($monthlyUsage, 2),
            'min' => (int) ceil($monthlyUsage * 1),
            'reorder' => (int) ceil($monthlyUsage * 2),
            'max' => (int) ceil($monthlyUsage * 3),
        ];
    }

    /**
     * Determine stock level alert status
     */
    public static function determineStatus(float|int $currentQty, float|int $min, float|int $reorder, float|int $max): string
    {
        if ($currentQty <= 0) {
            return 'TIADA_STOK';
        }
        if ($currentQty <= $min) {
            return 'BELOW_MIN';
        }
        if ($currentQty <= $reorder) {
            return 'REORDER';
        }
        if ($max > 0 && $currentQty > $max) {
            return 'ABOVE_MAX';
        }
        return 'NORMAL';
    }

    /**
     * Get aggregate statistics for stock levels across all items
     */
    public static function getLevelSummaries(): array
    {
        $items = StockItem::where('status', '!=', 'INACTIVE')->get();

        $belowMin = 0;
        $reorderReq = 0;
        $normal = 0;
        $aboveMax = 0;
        $outOfStock = 0;

        foreach ($items as $item) {
            $status = $item->stock_level_status['status'];
            if ($status === 'TIADA_STOK') $outOfStock++;
            elseif ($status === 'BAWAH_MINIMUM') $belowMin++;
            elseif ($status === 'MENOKOK') $reorderReq++;
            elseif ($status === 'LEBIH_MAKSIMUM') $aboveMax++;
            else $normal++;
        }

        return [
            'total_items' => $items->count(),
            'out_of_stock' => $outOfStock,
            'below_min' => $belowMin,
            'reorder_required' => $reorderReq,
            'normal' => $normal,
            'above_max' => $aboveMax,
        ];
    }
}
