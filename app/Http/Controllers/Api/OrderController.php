<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function checkout(Request $request)
    {
        // 1. Validasi input: Harus ada data barang yang dibeli
        $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1'
        ]);

        // 2. Gunakan DB Transaction (Agar kalau ada error di tengah jalan, semua dibatalkan/di-rollback)
        DB::beginTransaction();

        try {
            $totalPrice = 0;

            // 3. Buat kerangka Order utama terlebih dahulu (Total harga diset 0 dulu)
            $order = Order::create([
                'user_id' => auth()->id(), // Mengambil ID user yang sedang login dari Token
                'total_price' => 0,
                'status' => 'pending'
            ]);

            // 4. Proses setiap barang yang dibeli
            foreach ($request->items as $item) {
                // Cari produknya di database
                $product = Product::find($item['product_id']);

                // Cek apakah stoknya cukup?
                if ($product->stock < $item['quantity']) {
                    return response()->json([
                        'message' => 'Stok produk ' . $product->name . ' tidak mencukupi'
                    ], 400);
                }

                // Tambahkan total harga belanjaan
                $subTotal = $product->price * $item['quantity'];
                $totalPrice += $subTotal;

                // Simpan rincian barang ke tabel order_items
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price
                ]);

                // 5. Kurangi stok barang di database utama
                $product->stock -= $item['quantity'];
                $product->save();
            }

            // 6. Update total harga asli ke Order utama
            $order->update([
                'total_price' => $totalPrice
            ]);

            // Simpan semua perubahan transaksi ke database
            DB::commit();

            // Panggil relasi items agar terlihat di response JSON
            $order->load('items');

            return response()->json([
                'status' => 'success',
                'message' => 'Checkout berhasil dilakukan',
                'data' => $order
            ], 201);

        } catch (\Exception $e) {
            // Kalau ada error di tengah proses, batalkan semua perintah database di atas
            DB::rollback();
            return response()->json(['message' => 'Terjadi kesalahan sistem', 'error' => $e->getMessage()], 500);
        }
    }
}