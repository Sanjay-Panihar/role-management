<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\Product;
use Auth;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::where('created_by', Auth::id())
        ->when($request->has('product_code'), function ($query) use ($request) {
            return $query->where('product_code', 'like', '%' . $request->input('product_code') . '%');
        })
        ->when($request->has('product_name'), function ($query) use ($request) {
            return $query->where('name', 'like', '%' . $request->input('product_name') . '%');
        })
        ->paginate(10);
    
    $products->each(function ($product) {
        $product->image = json_decode($product->image, true);
    });
    
    return view('products.index')->with(['products' => $products]);
    
    }
    public function create()
    {
        return view('products.create');
    }
    public function store(Request $request)
    {
        $validationData = [
            'name' => "required|string|max:100",
            'product_code' => "required|string|max:100",
            'description' => "required|string",
            'stock' => "required|string|max:100",
            'price' => "required|string|max:100",
        ];
        if ($request->hasFile('image')) {
            $validationData['image.*'] = 'required|mimes:jpg,jpeg,png|max:2048';
        }
        $validator = Validator::make($request->all(), $validationData);
    
        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()->all()], 422);
        } else {
            $id = $request->id ?? 0;
            $action = $id ? 'updated' : 'created';
            $productData = $request->only(['name', 'product_code', 'description', 'stock', 'price']);
            $productData['created_by'] = Auth::id();
            if ($request->hasFile('image')) {
                $images = $request->file('image');
                $imageNames = [];
                
                foreach ($images as $image) {
                    $imageName = mt_rand(10000000,99999999).'_.'.$image->getClientOriginalName();
                    $image->storeAs('public/images', $imageName);
                    $imageNames[] = $imageName;
                }
                
                $imageJson = json_encode($imageNames);
                
                $productData['image'] = $imageJson;
            }
            Product::updateOrCreate(
                ['id' => $id],
                $productData
            );
    
            return response()->json(['status' => true, 'message' => "Product $action successfully."]);
        }
    }
    public function destroy(Request $request)
    {
        $product = Product::find($request->id)->delete();

        if($product)
        {
            return response()->json(['status' => true, 'message'=> 'Product deleted successfully.']);
        } else {
            return response()->json(['status' => false, 'error' => 'No product found.']);
        }
    }
    public function edit(Request $request)
    {
        $product = Product::select('id', 'name', 'product_code', 'description', 'stock', 'price', 'image')->find($request->id);
        $product->image = json_decode($product->image);

        return response()->json(['status'=> true, 'product' => $product]);
    }
}
