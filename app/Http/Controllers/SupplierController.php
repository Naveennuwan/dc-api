<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierRequest;
use App\Models\Supplier;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    public function GetAll()
    {
        return Supplier::where('is_deleted', false)
            ->get();
    }

    public function GetActive()
    {
        return Supplier::where('is_active', true)
            ->where('is_deleted', false)
            ->get();
    }

    public function GetById($id)
    {
        $supplier = Supplier::find($id);

        if ($supplier) {
            return response()->json($supplier);
        } else {
            return response()->json(['message' => 'Supplier not found'], 404);
        }
    }

    public function Store(SupplierRequest $request)
    {
        $user = Auth::user();
        $supplier = new Supplier;

        $supplier->supplier = $request->input('supplier');
        $supplier->supplier_contact = $request->input('supplier_contact');
        $supplier->is_active = $request->input('is_active') ?? true;
        $supplier->created_by = $user->id;

        if ($supplier->save()) {
            return response()->json(['message' => 'Supplier created successfully']);
        } else {
            return response()->json(['message' => 'Failed to create Supplier'], 500);
        }
    }

    public function Update(SupplierRequest $request, $id)
    {
        $user = Auth::user();
        $supplier = Supplier::find($id);

        if ($supplier) {
            $supplier->supplier = $request->input('supplier');
            $supplier->supplier_contact = $request->input('supplier_contact');
            $supplier->is_active = $request->input('is_active') ?? true;
            $supplier->updated_by = $user->id;

            if ($supplier->save()) {
                return response()->json(['message' => 'Supplier updated successfully']);
            } else {
                return response()->json(['message' => 'Failed to update Supplier'], 500);
            }
        } else {
            return response()->json(['message' => 'Supplier not found'], 404);
        }
    }

    public function SoftDelete($id)
    {
        $user = Auth::user();
        $supplier = Supplier::find($id);

        if ($supplier) {
            $supplier->is_deleted = true;
            $supplier->deleted_by = $user->id;
            $supplier->deleted_at = now();

            if ($supplier->save()) {
                return response()->json(['message' => 'Supplier deleted successfully']);
            } else {
                return response()->json(['message' => 'Failed to delete Supplier'], 500);
            }
        } else {
            return response()->json(['message' => 'Supplier not found'], 404);
        }
    }
}
