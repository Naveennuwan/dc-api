<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Products;
use Illuminate\Support\Facades\Auth;

class ProductsController extends Controller
{
    public function GetAll()
    {
        return Products::where('is_deleted', false)
            ->get();
    }

    public function GetActive()
    {
        return Products::where('is_active', true)
            ->where('is_deleted', false)
            ->get();
    }

    public function GetById($id)
    {
        $product = Products::find($id);

        if ($product) {
            return response()->json($product);
        } else {
            return response()->json(['message' => 'Products not found'], 404);
        }
    }

    public function Store(ProductRequest $request)
    {
        $user = Auth::user();
        $product = new Products;

        $product->product = $request->input('product');
        $product->description = $request->input('description');
        $product->brand_id = $request->input('brand_id');
        $product->category_id = $request->input('category_id');
        $product->supplier_id = $request->input('supplier_id');
        $product->is_active = $request->input('is_active') ?? true;
        $product->created_by = $user->id;

        if ($product->save()) {
            return response()->json(['message' => 'Products created successfully']);
        } else {
            return response()->json(['message' => 'Failed to create Products'], 500);
        }
    }

    public function Update(ProductRequest $request, $id)
    {
        $user = Auth::user();
        $product = Products::find($id);

        if ($product) {
            $product->product = $request->input('product');
            $product->description = $request->input('description');
            $product->brand_id = $request->input('brand_id');
            $product->category_id = $request->input('category_id');
            $product->supplier_id = $request->input('supplier_id');
            $product->is_active = $request->input('is_active') ?? true;
            $product->updated_by = $user->id;

            if ($product->save()) {
                return response()->json(['message' => 'Products updated successfully']);
            } else {
                return response()->json(['message' => 'Failed to update Products'], 500);
            }
        } else {
            return response()->json(['message' => 'Products not found'], 404);
        }
    }

    public function SoftDelete($id)
    {
        $user = Auth::user();
        $product = Products::find($id);

        if ($product) {
            $product->is_deleted = true;
            $product->deleted_by = $user->id;
            $product->deleted_at = now();

            if ($product->save()) {
                return response()->json(['message' => 'Products deleted successfully']);
            } else {
                return response()->json(['message' => 'Failed to delete Products'], 500);
            }
        } else {
            return response()->json(['message' => 'Products not found'], 404);
        }
    }
}
