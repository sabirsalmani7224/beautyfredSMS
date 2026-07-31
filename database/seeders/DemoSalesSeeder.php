<?php

namespace Database\Seeders;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Shop;
use App\Models\Product;
use App\Models\User;
use App\Models\UserStock;
use App\Models\Target;
use Illuminate\Database\Seeder;

class DemoSalesSeeder extends Seeder
{
    public function run(): void
    {
        $psms    = User::where('role', 'psm')->get();
        $products = Product::where('is_active', true)->get();

        if ($psms->isEmpty()) {
            $this->command->warn('No PSMs found — run DemoHierarchySeeder first.');
            return;
        }

        // Give each PSM some stock
        foreach ($psms as $psm) {
            foreach ($products->random(min(15, $products->count())) as $product) {
                UserStock::updateOrCreate(
                    ['user_id' => $psm->id, 'product_id' => $product->id],
                    ['quantity' => rand(20, 100)]
                );
            }
        }

        // Create shops for PSMs
        $shopNames = ['Beauty Palace', 'Glow Studio', 'Skin Oasis', 'Radiance Mart', 'Wellness Hub'];
        $shops = [];
        foreach ($psms as $psm) {
            foreach (array_slice($shopNames, 0, 2) as $i => $shopName) {
                $shops[] = Shop::updateOrCreate(
                    ['name' => $shopName . ' (' . $psm->city . ')', 'registered_by' => $psm->id],
                    [
                        'owner_name'  => 'Owner ' . ($i + 1),
                        'phone'       => '9870' . rand(100000, 999999),
                        'city'        => $psm->city ?? 'Mumbai',
                        'state'       => $psm->state ?? 'Maharashtra',
                        'is_active'   => true,
                    ]
                );
            }
        }

        // Generate sales for last 3 months
        $statuses = ['approved', 'approved', 'approved', 'pending', 'rejected'];
        $paymentModes = ['cash', 'upi', 'credit', 'cheque', 'mixed'];
        $saleCount = 0;

        foreach ($psms as $psm) {
            $psmShops = Shop::where('registered_by', $psm->id)->get();
            $psmStock = UserStock::where('user_id', $psm->id)->where('quantity', '>', 5)->with('product')->get();

            if ($psmStock->isEmpty() || $psmShops->isEmpty()) continue;

            for ($day = 90; $day >= 1; $day--) {
                // Each PSM records 0-3 sales per day
                $salesPerDay = rand(0, 3);
                for ($s = 0; $s < $salesPerDay; $s++) {
                    $shop   = $psmShops->random();
                    $status = $statuses[array_rand($statuses)];
                    $items  = $psmStock->random(rand(1, min(4, $psmStock->count())));

                    $totalAmount = 0;
                    $itemData    = [];

                    foreach ($items as $stock) {
                        $qty        = rand(1, 5);
                        $unitPrice  = (float) $stock->product->unit_price;
                        $lineTotal  = $qty * $unitPrice;
                        $totalAmount += $lineTotal;
                        $itemData[] = [
                            'product_id'  => $stock->product_id,
                            'quantity'    => $qty,
                            'unit_price'  => $unitPrice,
                            'total_price' => $lineTotal,
                        ];
                    }

                    $sale = Sale::create([
                        'user_id'       => $psm->id,
                        'shop_id'       => $shop->id,
                        'sale_type'     => 'shop_sale',
                        'sale_date'     => now()->subDays($day)->toDateString(),
                        'total_amount'  => $totalAmount,
                        'discount_amount'=> 0,
                        'net_amount'    => $totalAmount,
                        'payment_mode'  => $paymentModes[array_rand($paymentModes)],
                        'status'        => $status,
                        'approved_by'   => $status !== 'pending' ? User::where('parent_id', $psm->id)->orWhere('id', $psm->parent_id)->value('id') : null,
                        'approved_at'   => $status !== 'pending' ? now()->subDays($day)->addHours(2) : null,
                    ]);

                    foreach ($itemData as $item) {
                        SaleItem::create(array_merge($item, ['sale_id' => $sale->id]));
                    }

                    $saleCount++;
                }
            }
        }

        // Set monthly targets for TLs and PSMs
        foreach (User::whereIn('role', ['tl', 'psm'])->get() as $user) {
            for ($m = -2; $m <= 1; $m++) {
                $date = now()->addMonths($m);
                Target::updateOrCreate(
                    ['user_id' => $user->id, 'category_id' => null, 'month' => $date->month, 'year' => $date->year],
                    [
                        'set_by_user_id' => $user->parent_id,
                        'target_amount'  => rand(50, 200) * 1000,
                        'target_quantity' => rand(100, 500),
                    ]
                );
            }
        }

        $this->command->info("✅ Demo sales seeded: {$saleCount} sales across " . $psms->count() . " PSMs");
        $this->command->info('   Shops: ' . Shop::count() . ' | Sales: ' . Sale::count() . ' | Targets: ' . Target::count());
    }
}
