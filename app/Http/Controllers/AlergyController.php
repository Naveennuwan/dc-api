<?php

namespace App\Http\Controllers;

use App\Http\Requests\AlergyRequest;
use App\Models\Alergy;
use Illuminate\Support\Facades\Auth;

class AlergyController extends Controller
{
    public function GetAll()
    {
        return Alergy::where('is_deleted', false)
            ->get();
    }

    public function GetActive()
    {
        return Alergy::where('is_active', true)
            ->where('is_deleted', false)
            ->get();
    }

    public function GetById($id)
    {
        $alergy = Alergy::find($id);

        if ($alergy) {
            return response()->json($alergy);
        } else {
            return response()->json(['message' => 'Alergy not found'], 404);
        }
    }

    public function Store(AlergyRequest $request)
    {
        $user = Auth::user();
        $alergy = new Alergy;

        $alergy->alergy_name = $request->input('alergy_name');
        $alergy->is_active = $request->input('is_active') ?? true;
        $alergy->created_by = $user->id;

        if ($alergy->save()) {
            return response()->json(['message' => 'Alergy created successfully']);
        } else {
            return response()->json(['message' => 'Failed to create Alergy'], 500);
        }
    }

    public function Update(AlergyRequest $request, $id)
    {
        $user = Auth::user();
        $alergy = Alergy::find($id);

        if ($alergy) {
            $alergy->alergy_name = $request->input('alergy_name');
            $alergy->is_active = $request->input('is_active') ?? true;
            $alergy->updated_by = $user->id;

            if ($alergy->save()) {
                return response()->json(['message' => 'Alergy updated successfully']);
            } else {
                return response()->json(['message' => 'Failed to update Alergy'], 500);
            }
        } else {
            return response()->json(['message' => 'Alergy not found'], 404);
        }
    }

    public function SoftDelete($id)
    {
        $user = Auth::user();
        $alergy = Alergy::find($id);

        if ($alergy) {
            $alergy->is_deleted = true;
            $alergy->deleted_by = $user->id;
            $alergy->deleted_at = now();

            if ($alergy->save()) {
                return response()->json(['message' => 'Alergy deleted successfully']);
            } else {
                return response()->json(['message' => 'Failed to delete Alergy'], 500);
            }
        } else {
            return response()->json(['message' => 'Alergy not found'], 404);
        }
    }
}
