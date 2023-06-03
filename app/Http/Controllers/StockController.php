<?php

namespace App\Http\Controllers;

use App\Http\Requests\StockRequest;
use App\Models\Stock;
use Illuminate\Support\Facades\Auth;

class StockController extends Controller
{
    public function GetAll($centerid)
    {
        return Stock::where('quantity', '>', 0)
            ->where('center_id', $centerid)
            ->get();
    }

    public function Store(StockRequest $request)
    {
        $user = Auth::user();
        $stock = new Stock;

        $stock->product_id = $request->input('product_id');
        $stock->expire_date = $request->input('expire_date');
        $stock->quantity = $request->input('quantity');
        $stock->price = $request->input('price');
        $stock->selling_price = $request->input('selling_price');
        $stock->center_id = $request->input('center_id');
        $stock->created_by = $user->id;

        if ($stock->save()) {
            return response()->json(['message' => 'Stock added successfully']);
        } else {
            return response()->json(['message' => 'Failed to add stock'], 500);
        }
    }
}
