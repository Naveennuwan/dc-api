<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasterData;

class MasterDataController extends Controller
{
    public function GetAll()
    {
        return MasterData::all();
    }


    public function Update(Request $request, $id)
    {
        $Data = MasterData::find($id);

        if ($Data) {
            $Data->profite = $request->input('profite');
            $Data->discount = $request->input('discount');

            if ($Data->save()) {
                return response()->json(['message' => 'Data updated successfully']);
            } else {
                return response()->json(['message' => 'Failed to update Data'], 500);
            }
        } else {
            return response()->json(['message' => 'Data not found'], 404);
        }
    }
}
