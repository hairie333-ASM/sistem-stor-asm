<?php

namespace App\Console\Commands;

use App\Models\StockItem;
use Illuminate\Console\Command;

class LinkStockImagesCommand extends Command
{
    protected $signature = 'stock:link-images';
    protected $description = 'Pautkan gambar produk katalog sebenar kepada senarai kod stok';

    public function handle(): int
    {
        $this->info('Memautkan gambar katalog ke item stok...');
        
        $items = StockItem::all();
        $updated = 0;

        foreach ($items as $item) {
            $code = $item->getCatalogCode();
            if ($code && file_exists(public_path("images/stocks/{$code}.png"))) {
                $item->image_url = "images/stocks/{$code}.png";
                $item->save();
                $updated++;
            }
        }

        $this->info("Berjaya memautkan {$updated} item stok dengan gambar produk katalog!");
        return Command::SUCCESS;
    }
}
