<?php

namespace App\Http\Controllers;

use App\Http\Requests\DiseaseRequest;
use App\Models\Disease;
use Illuminate\Support\Facades\Auth;

class DiseaseController extends Controller
{
    public function GetAll()
    {
        return Disease::where('is_deleted', false)
            ->get();
    }

    public function GetActive()
    {
        return Disease::where('is_active', true)
            ->where('is_deleted', false)
            ->get();
    }

    public function GetById($id)
    {
        $disease = Disease::find($id);

        if ($disease) {
            return response()->json($disease);
        } else {
            return response()->json(['message' => 'Disease not found'], 404);
        }
    }

    public function Store(DiseaseRequest $request)
    {
        $user = Auth::user();
        $disease = new Disease;

        $disease->disease_name = $request->input('disease_name');
        $disease->is_active = $request->input('is_active') ?? true;
        $disease->created_by = $user->id;

        if ($disease->save()) {
            return response()->json(['message' => 'Disease created successfully']);
        } else {
            return response()->json(['message' => 'Failed to create Disease'], 500);
        }
    }

    public function Update(DiseaseRequest $request, $id)
    {
        $user = Auth::user();
        $disease = Disease::find($id);

        if ($disease) {
            $disease->disease_name = $request->input('disease_name');
            $disease->is_active = $request->input('is_active') ?? true;
            $disease->updated_by = $user->id;

            if ($disease->save()) {
                return response()->json(['message' => 'Disease updated successfully']);
            } else {
                return response()->json(['message' => 'Failed to update Disease'], 500);
            }
        } else {
            return response()->json(['message' => 'Disease not found'], 404);
        }
    }

    public function SoftDelete($id)
    {
        $user = Auth::user();
        $disease = Disease::find($id);

        if ($disease) {
            $disease->is_deleted = true;
            $disease->deleted_by = $user->id;
            $disease->deleted_at = now();

            if ($disease->save()) {
                return response()->json(['message' => 'Disease deleted successfully']);
            } else {
                return response()->json(['message' => 'Failed to delete Disease'], 500);
            }
        } else {
            return response()->json(['message' => 'Disease not found'], 404);
        }
    }
}
