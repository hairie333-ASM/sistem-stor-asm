<?php

namespace App\Services;

use App\Models\Receiving;
use App\Models\Rejection;
use App\Models\StockRequest;
use App\Models\Packing;
use App\Models\StockTransfer;
use App\Models\StockVerification;
use App\Models\StockAdjustment;
use App\Models\Disposal;
use App\Models\LossCase;
use App\Models\StockTransaction;
use App\Models\SystemSetting;

class DocumentNumberService
{
    public static function generate(string $type): string
    {
        $year = date('Y');
        $org = SystemSetting::get('org_code', 'ASM');

        switch ($type) {
            case 'BTB': // KEW.PS-1
                $count = Receiving::whereYear('created_at', $year)->count() + 1;
                return sprintf("BTB/%s/%s/%04d", $org, $year, $count);

            case 'BPB': // KEW.PS-2
                $count = Rejection::whereYear('created_at', $year)->count() + 1;
                return sprintf("BPB/%s/%s/%04d", $org, $year, $count);

            case 'PS7': // KEW.PS-7
                $count = StockRequest::where('form_type', 'KEW.PS-7')->whereYear('created_at', $year)->count() + 1;
                return sprintf("PS7/%s/%s/%04d", $org, $year, $count);

            case 'PS8': // KEW.PS-8
                $count = StockRequest::where('form_type', 'KEW.PS-8')->whereYear('created_at', $year)->count() + 1;
                return sprintf("PS8/%s/%s/%04d", $org, $year, $count);

            case 'PS9': // KEW.PS-9
                $count = Packing::whereYear('created_at', $year)->count() + 1;
                return sprintf("PS9/%s/%s/%04d", $org, $year, $count);

            case 'PS11': // KEW.PS-11
                $count = StockVerification::whereYear('created_at', $year)->count() + 1;
                return sprintf("PS11/%s/%s/%04d", $org, $year, $count);

            case 'PS15': // KEW.PS-15
                $count = StockAdjustment::whereYear('created_at', $year)->count() + 1;
                return sprintf("PS15/%s/%s/%04d", $org, $year, $count);

            case 'PS17': // KEW.PS-17
                $count = StockTransfer::whereYear('created_at', $year)->count() + 1;
                return sprintf("PS17/%s/%s/%04d", $org, $year, $count);

            case 'PS20': // KEW.PS-20
                $count = Disposal::whereYear('created_at', $year)->count() + 1;
                return sprintf("PS20/%s/%s/%04d", $org, $year, $count);

            case 'PS32': // KEW.PS-32
                $count = LossCase::whereYear('created_at', $year)->count() + 1;
                return sprintf("PS32/%s/%s/%04d", $org, $year, $count);

            case 'TXN': // Transaction Ledger
                $count = StockTransaction::whereYear('created_at', $year)->count() + 1;
                return sprintf("TXN/%s/%s/%05d", $org, $year, $count);

            default:
                return sprintf("DOC/%s/%s/%04d", $org, $year, rand(1000, 9999));
        }
    }
}
