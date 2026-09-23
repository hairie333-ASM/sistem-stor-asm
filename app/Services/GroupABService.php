<?php

namespace App\Services;

use App\Models\StockItem;
use App\Models\StockTransactionItem;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class GroupABService
{
    /**
     * Compute Group A and Group B classifications based on TPS 30% / 70% rule
     */
    public static function calculateGroups(): array
    {
        $oneYearAgo = Carbon::now()->subYear();

        $items = StockItem::with('category', 'uom')->get();
        $itemStats = new Collection();

        foreach ($items as $item) {
            // Calculate total receipt/purchase value in past 12 months
            $purchaseValue = StockTransactionItem::where('stock_item_id', $item->id)
                ->where('movement_type', 'IN')
                ->whereHas('transaction', function ($q) use ($oneYearAgo) {
                    $q->whereIn('transaction_type', ['RECEIPT'])
                      ->where('transaction_date', '>=', $oneYearAgo);
                })
                ->sum('total_price');

            // If no purchases recorded this year, fallback to current stock value for calculation
            if ($purchaseValue <= 0) {
                $purchaseValue = (float) ($item->current_quantity * $item->unit_price);
            }

            $itemStats->push([
                'item' => $item,
                'annual_value' => (float) $purchaseValue,
            ]);
        }

        // Sort descending by value
        $sorted = $itemStats->sortByDesc('annual_value')->values();
        $totalItemsCount = $sorted->count();
        $groupACount = (int) ceil($totalItemsCount * 0.30); // 30%

        $groupA = new Collection();
        $groupB = new Collection();

        foreach ($sorted as $index => $row) {
            $isGroupA = ($index < $groupACount);
            $newGroup = $isGroupA ? 'A' : 'B';
            
            $item = $row['item'];
            $oldGroup = $item->stock_group;

            $groupData = array_merge($row, [
                'current_group' => $newGroup,
                'previous_group' => $oldGroup,
                'is_changed' => ($oldGroup !== $newGroup),
            ]);

            if ($isGroupA) {
                $groupA->push($groupData);
            } else {
                $groupB->push($groupData);
            }
        }

        return [
            'total_items' => $totalItemsCount,
            'group_a_count' => $groupA->count(),
            'group_b_count' => $groupB->count(),
            'group_a' => $groupA,
            'group_b' => $groupB,
        ];
    }

    /**
     * Apply and save the Group A/B classification to database
     */
    public static function applyGroupClassification(): void
    {
        $results = self::calculateGroups();

        foreach ($results['group_a'] as $row) {
            $item = $row['item'];
            $item->stock_group = 'A';
            $item->save();
        }

        foreach ($results['group_b'] as $row) {
            $item = $row['item'];
            $item->stock_group = 'B';
            $item->save();
        }

        AuditLogService::log(
            'CLASSIFY_GROUP_AB',
            'StockItem',
            null,
            null,
            ['group_a_count' => $results['group_a_count'], 'group_b_count' => $results['group_b_count']],
            'Penentuan Kumpulan A & B (KEW.PS-5) dikemaskini mengikut peraturan 30% / 70% TPS.'
        );
    }
}
