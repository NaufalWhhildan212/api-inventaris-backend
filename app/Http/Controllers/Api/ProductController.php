<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Menampilkan semua produk
    public function index()
    {
        $products = Product::all();
        return response()->json([
            'status' => 'success',
            'data' => $products
        ], 200);
    }

    // Menambah produk baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|integer',
            'stock' => 'required|integer',
        ]);

        $product = Product::create($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Produk pakan hewan berhasil ditambahkan',
            'data' => $product
        ], 201);
    }

    // Menampilkan satu produk spesifik
    public function show($id)
    {
        $product = Product::find($id);
        
        if (!$product) {
            return response()->json(['message' => 'Produk tidak ditemukan'], 404);
        }

        return response()->json(['data' => $product], 200);
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Produk tidak ditemukan'], 404);
        }

        // Mengupdate data sesuai input yang dikirim
        $product->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Data pakan hewan berhasil diubah',
            'data' => $product
        ], 200);
    }

    // Menghapus produk (Delete)
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Produk tidak ditemukan'], 404);
        }

        $product->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Produk pakan hewan berhasil dihapus'
        ], 200);
    }
}