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

// public function store(Request $request)
// {
//     // Log if the request has a file
//     Log::info('Request has file "image": ' . ($request->hasFile('image') ? 'yes' : 'no'));

//     // Validate request data
//     $data = $request->validate([
//         'name' => 'required|string|max:255',
//         'price' => 'required|numeric',
//         'description' => 'nullable|string',
//         'image' => 'nullable|image|mimes:jpeg,png,jpg|max:5120', // 5MB max
//     ]);

//     // Handle image upload if present
//     if ($request->hasFile('image')) {
//         $imagePath = $request->file('image')->store('products', 'public');
//         Log::info("Image successfully stored at: " . $imagePath);
//         $data['image'] = $imagePath;
//     } else {
//         Log::info("No image uploaded.");
//     }

//     // Create product in DB
//     $product = Product::create($data);

//     // Return response with optional image URL
//     return response()->json([
//         'message' => 'Product created successfully',
//         'product' => $product,
//         'image_url' => isset($product->image) ? asset('storage/' . $product->image) : null,
//     ], 201);
// }


public function store(Request $request)
{
    Log::info('Request has file "image": ' . ($request->hasFile('image') ? 'yes' : 'no'));

    $data = $request->validate([
        'name' => 'required|string|max:255',
        'price' => 'required|numeric',
        'description' => 'nullable|string',
        'image' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        'stock' => 'required|string|max:255',
    ]);

    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('products', 'public');
        Log::info("Image successfully stored at: " . $imagePath);
        $data['image'] = $imagePath;
    } else {
        Log::info("No image uploaded.");
    }

    $product = Product::create($data);

    return response()->json([
        'message' => 'Product created successfully',
        'product' => $product,
        'image_url' => isset($product->image) ? asset('storage/' . $product->image) : null,
    ], 201);
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
        'stock' => 'required|string|max:255',
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
