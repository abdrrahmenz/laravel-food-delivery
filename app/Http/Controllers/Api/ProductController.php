<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    //index
    public function index(Request $request)
    {

        //get product by request user id
        $products = Product::with('user')->where('user_id', $request->user()->id)->get();

        // $products = Product::with('user')->whereHas('user', function ($query) use ($request) {
        //     $query->where('roles', 'restaurant');
        // })->get();


        return response()->json([
            'status' => 'success',
            'message' => 'List data products',
            'data' => $products
        ]);
    }

    //get product by user_id
    public function getProductByUserId(Request $request)
    {
        $products = Product::with('user')->where('user_id', $request->user_id)->get();

        return response()->json([
            'status' => 'success',
            'message' => 'List data products by user_id',
            'data' => $products
        ]);
    }

    //store
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|integer',
            'stock' => 'required|integer',
            'is_available' => 'required|boolean',
            'is_favorite' => 'required|boolean',
            'image' => 'required|image',
        ], [
            'name.required' => 'The name field is required.',
            'description.required' => 'The description field is required.',
            'price.required' => 'The price field is required.',
            'stock.required' => 'The stock field is required.',
            'is_available.required' => 'The is_available field is required.',
            'is_favorite.required' => 'The is_favorite field is required.',
            'image.required' => 'The image field is required.',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = $request->user();
        $request->merge(['user_id' => $user->id]);

        $data = $request->all();

        $product = Product::create($data);

        //check if image is available
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $image_name = time() . '.' . $image->getClientOriginalExtension();
            $image->move('uploads/products', $image_name);

            $product->image = $image_name;
            $product->save();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Product added successfully',
            'data' => $product
        ]);
    }

    //update
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|integer',
            'stock' => 'required|integer',
            'is_available' => 'required|boolean',
            'is_favorite' => 'required|boolean',
        ]);

        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found',
            ], 404);
        }

        $data = $request->all();

        $product->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Product updated successfully',
            'data' => $product
        ]);
    }

    //destroy
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found',
            ], 404);
        }

        $product->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Product deleted successfully',
        ]);
    }
}
