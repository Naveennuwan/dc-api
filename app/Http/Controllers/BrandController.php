<?php

namespace App\Http\Controllers;

use App\Http\Requests\BrandRequest;
use App\Models\Brand;
use Illuminate\Support\Facades\Auth;

class BrandController extends Controller
{
    public function GetAll()
    {
        return Brand::where('is_deleted', false)
            ->get();
    }

    public function GetActive()
    {
        return Brand::where('is_active', true)
            ->where('is_deleted', false)
            ->get();
    }

    public function GetById($id)
    {
        $brand = Brand::find($id);

        if ($brand) {
            return response()->json($brand);
        } else {
            return response()->json(['message' => 'Brand not found'], 404);
        }
    }

    public function Store(BrandRequest $request)
    {
        $user = Auth::user();
        $brand = new Brand;

        $brand->brand = $request->input('brand');
        $brand->is_active = $request->input('is_active') ?? true;
        $brand->created_by = $user->id;

        if ($brand->save()) {
            return response()->json(['message' => 'Brand created successfully']);
        } else {
            return response()->json(['message' => 'Failed to create Brand'], 500);
        }
    }

    public function Update(BrandRequest $request, $id)
    {
        $user = Auth::user();
        $brand = Brand::find($id);

        if ($brand) {
            $brand->brand = $request->input('brand');
            $brand->is_active = $request->input('is_active') ?? true;
            $brand->updated_by = $user->id;

            if ($brand->save()) {
                return response()->json(['message' => 'Brand updated successfully']);
            } else {
                return response()->json(['message' => 'Failed to update Brand'], 500);
            }
        } else {
            return response()->json(['message' => 'Brand not found'], 404);
        }
    }

    public function SoftDelete($id)
    {
        $user = Auth::user();
        $brand = Brand::find($id);

        if ($brand) {
            $brand->is_deleted = true;
            $brand->deleted_by = $user->id;
            $brand->deleted_at = now();

            if ($brand->save()) {
                return response()->json(['message' => 'Brand deleted successfully']);
            } else {
                return response()->json(['message' => 'Failed to delete Brand'], 500);
            }
        } else {
            return response()->json(['message' => 'Brand not found'], 404);
        }
    }
}
