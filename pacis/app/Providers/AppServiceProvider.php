<?php

namespace App\Providers;

use App\Services\Barcode\BarcodeGenerator;
use App\Services\Csf\CsfParser;
use App\Services\Folios\FolioGenerator;
use App\Services\Inventory\StockService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(StockService::class);
        $this->app->singleton(CsfParser::class);
        $this->app->singleton(BarcodeGenerator::class);
        $this->app->singleton(FolioGenerator::class);
    }

    public function boot(): void
    {
        Paginator::useTailwind();
    }
}
