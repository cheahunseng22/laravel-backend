<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{

    public function index()
{
    return Product::all();
}

public function store(Request $request)
{
    $data = $request->validate([
        'name' => 'required|string',
        'price' => 'required|numeric',
        'description' => 'nullable|string',
        'image' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
    ]);

if ($request->hasFile('image')) {
    $path = $request->file('image')->store('products', 'public');
    Log::info("Image saved at: " . $path);
    $data['image'] = $path;
}

    return Product::create($data);
}

public function show(Product $product)
{
    return $product;
}

public function update(Request $request, Product $product)
{
    $data = $request->validate([
        'name' => 'required|string',
        'price' => 'required|numeric',
        'description' => 'nullable|string',
        'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    ]);

    if ($request->hasFile('image')) {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        $data['image'] = $request->file('image')->store('products', 'public');
    }

    $product->update($data);
    return $product;
}

public function destroy(Product $product)
{
    if ($product->image) {
        Storage::disk('public')->delete($product->image);
    }

    $product->delete();
    return response()->json(['message' => 'Product deleted']);
}

}
