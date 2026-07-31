<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockAllocation;
use App\Models\StockAllocationItem;
use App\Models\UserStock;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class StockService
{
    /**
     * Get current stock balance for a user (all products).
     */
    public function getUserStock(User $user): \Illuminate\Database\Eloquent\Collection
    {
        return UserStock::with('product.subCategory.category')
            ->where('user_id', $user->id)
            ->where('quantity', '>', 0)
            ->orderByDesc('quantity')
            ->get();
    }

    /**
     * Get current stock for a specific product and user.
     */
    public function getProductStock(User $user, int $productId): int
    {
        return UserStock::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->value('quantity') ?? 0;
    }

    /**
     * Allocate stock from one user to another.
     * Deducts from sender, creates allocation record.
     */
    public function allocateStock(
        User $fromUser,
        User $toUser,
        array $items, // [['product_id' => X, 'quantity' => Y], ...]
        ?int $warehouseId = null,
        ?string $notes = null
    ): StockAllocation {
        return DB::transaction(function () use ($fromUser, $toUser, $items, $warehouseId, $notes) {
            // Validate stock availability
            foreach ($items as $item) {
                $available = $this->getProductStock($fromUser, $item['product_id']);
                if ($available < $item['quantity']) {
                    $product = Product::find($item['product_id']);
                    throw new \Exception("Insufficient stock for {$product->name}. Available: {$available}, Requested: {$item['quantity']}");
                }
            }

            // Create allocation header
            $allocation = StockAllocation::create([
                'from_user_id'      => $fromUser->id,
                'to_user_id'        => $toUser->id,
                'from_warehouse_id' => $warehouseId,
                'status'            => 'dispatched',
                'dispatch_date'     => today(),
                'notes'             => $notes,
                'created_by'        => auth()->id(),
            ]);

            // Create line items and deduct from sender
            foreach ($items as $item) {
                StockAllocationItem::create([
                    'stock_allocation_id' => $allocation->id,
                    'product_id'          => $item['product_id'],
                    'quantity_sent'       => $item['quantity'],
                    'quantity_received'   => 0,
                ]);

                // Deduct from sender
                $this->adjustStock($fromUser->id, $item['product_id'], -$item['quantity']);
            }

            return $allocation;
        });
    }

    /**
     * Confirm receipt of an allocation — adds stock to receiver.
     */
    public function receiveAllocation(StockAllocation $allocation, array $receivedQtys = []): void
    {
        DB::transaction(function () use ($allocation, $receivedQtys) {
            $allocation->update([
                'status'        => 'received',
                'received_date' => today(),
            ]);

            foreach ($allocation->items as $item) {
                $received = $receivedQtys[$item->product_id] ?? $item->quantity_sent;
                $damaged  = $item->quantity_sent - $received;

                $item->update([
                    'quantity_received' => $received,
                    'quantity_damaged'  => max(0, $damaged),
                ]);

                // Add to receiver's stock
                $this->adjustStock($allocation->to_user_id, $item->product_id, $received);
            }
        });
    }

    /**
     * Deduct stock when a sale is made.
     */
    public function deductSaleStock(User $user, array $saleItems): void
    {
        DB::transaction(function () use ($user, $saleItems) {
            foreach ($saleItems as $item) {
                $current = $this->getProductStock($user, $item['product_id']);
                if ($current < $item['quantity']) {
                    $product = Product::find($item['product_id']);
                    throw new \Exception("Insufficient stock for {$product->name}");
                }
                $this->adjustStock($user->id, $item['product_id'], -$item['quantity']);
            }
        });
    }

    /**
     * Adjust (increment/decrement) stock for a user/product.
     */
    public function adjustStock(int $userId, int $productId, int $delta): void
    {
        UserStock::updateOrCreate(
            ['user_id' => $userId, 'product_id' => $productId],
            []
        );

        UserStock::where('user_id', $userId)
            ->where('product_id', $productId)
            ->increment('quantity', $delta, ['last_updated_at' => now()]);

        // Prevent negative stock
        UserStock::where('user_id', $userId)
            ->where('product_id', $productId)
            ->where('quantity', '<', 0)
            ->update(['quantity' => 0]);
    }
}
