<?php

namespace Tests\Unit;

use App\Models\Lot;
use App\Models\Product;
use App\Models\Remission;
use App\Models\RemissionItem;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\User;
use App\Models\Warehouse;
use App\Services\Inventory\StockService;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class StockServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_increase_creates_stock_and_movement(): void
    {
        $svc = app(StockService::class);
        $product   = Product::factory()->create();
        $warehouse = Warehouse::factory()->create();
        $lot       = Lot::factory()->for($product)->create();

        $svc->increase($product, $warehouse, $lot, 10, StockMovement::TYPE_PURCHASE, 25);

        $this->assertEquals(10, Stock::first()->quantity);
        $this->assertEquals(1,  StockMovement::count());
    }

    public function test_decrease_throws_when_insufficient_stock(): void
    {
        config(['pacis.inventory.allow_negative_stock' => false]);
        $svc = app(StockService::class);
        $product   = Product::factory()->create();
        $warehouse = Warehouse::factory()->create();

        $this->expectException(RuntimeException::class);
        $svc->decrease($product, $warehouse, null, 5);
    }

    public function test_revert_remission_returns_stock(): void
    {
        $svc       = app(StockService::class);
        $product   = Product::factory()->create();
        $warehouse = Warehouse::factory()->create();
        $lot       = Lot::factory()->for($product)->create();
        $user      = User::factory()->create();

        $svc->increase($product, $warehouse, $lot, 20, StockMovement::TYPE_PURCHASE, 10);
        $this->assertEquals(20, Stock::first()->quantity);

        $remission = Remission::factory()->create([
            'customer_id' => \App\Models\Customer::factory()->create()->id,
            'user_id'     => $user->id,
            'warehouse_id'=> $warehouse->id,
            'status'      => Remission::STATUS_OPEN,
        ]);

        $svc->decrease($product, $warehouse, $lot, 5, StockMovement::TYPE_REMISSION, $remission, $user->id);
        $this->assertEquals(15, Stock::first()->fresh()->quantity);

        $svc->revertRemission($remission->fresh(), $user->id, 'prueba');
        $this->assertEquals(20, Stock::first()->fresh()->quantity);
        $this->assertEquals(Remission::STATUS_CANCELLED, $remission->fresh()->status);
    }
}
