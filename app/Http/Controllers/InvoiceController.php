<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvoiceRequest;
use Illuminate\Http\Request;
use App\Models\Products;
use App\Models\Stock;
use App\Models\TemplateBody;
use App\Models\InvoiceHeader;
use App\Models\InvoiceBody;
use App\Models\MasterData;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class InvoiceController extends Controller
{

    public function abc(){
    //     return DB::table('invoice_bodies')
    // ->join('template_headers', 'template_headers.id', '=', 'invoice_bodies.template_header_id')
    // ->select('header_id', 'template_headers.template_name as name', DB::raw('SUM(selling_price) as price'), DB::raw('COUNT(template_body_id) as quantity'))
    // ->where('header_id', 5)
    // ->groupBy('template_body_id')
    // ->get();

        return InvoiceBody::with('templateHeader')
        // ->select('header_id', 'template_headers.template_name')
        ->selectRaw('SUM(selling_price) as price')
        ->selectRaw('COUNT(template_body_id) as quantity')
        ->where('header_id',5)
        ->groupBy('template_body_id')
        ->get();

        // return DB::select("
        // SELECT `header_id`, `template_headers`.`template_name`, SUM(`selling_price`) as total_price, COUNT(`template_body_id`) as quantity
        // FROM `invoice_bodies`
        // INNER JOIN `template_headers` ON `template_headers`.`id` = `invoice_bodies`.`template_header_id`
        // WHERE `header_id` = 5
        // GROUP BY `template_body_id`
        // ");
    }
    public function Index(InvoiceRequest $request)
    {
        $user = Auth::user();

        $total = 0;
        $profite = 0;
        $discount = 0;
        $center = $request['center_id'];
        //Check in stock
        foreach ($request->templates as $temp) {

            $templateBody = TemplateBody::where('template_id', $temp['id'])
                ->get();

            foreach ($templateBody as $body) {
                $sum = Stock::where('product_id', $body['product_id'])
                    ->where('center_id', $center)
                    ->sum('quantity');

                    if ($sum === null) {
                        $Product = Products::find($body['product_id']);
                        return response()->json(['message' => 'Stock not found for ' . $Product['product']], 500);
                    }
    
                    if ($sum < ($body['quantity'] * $temp['quantity'])) {
                        $Product = Products::find($body['product_id']);
                        return response()->json(['message' => 'Insuficient Stock in ' . $Product['product']], 500);
                    }
            }
        }

        //make Calculations

        $MasterData = MasterData::where('center_id', $center)->first();

        $profite = $MasterData->profite;
        $discount = $MasterData->discount;


        $maxId = InvoiceHeader::max('id');
        $CenterCode = "C" . str_pad($center, 2, '0', STR_PAD_LEFT);
        $UserCode = "U" . str_pad($user->id, 3, '0', STR_PAD_LEFT);
        $InvCode = "I" . str_pad($maxId + 1, 7, '0', STR_PAD_LEFT);

        $invoiceHeader = new InvoiceHeader;

        $invoiceHeader->invoice = $CenterCode . $UserCode . $InvCode;
        $invoiceHeader->center_id = $center;
        $invoiceHeader->discount = 0;
        $invoiceHeader->value = 0;
        $invoiceHeader->patient_id = $request->input('patient_Id');
        $invoiceHeader->created_by = $user->id;

        $invoiceHeader->save();

        foreach ($request->templates as $temp) {
            $price = 0;

            $templateBody = TemplateBody::where('template_id', $temp['id'])
                ->get();

            foreach ($templateBody as $body) {

                $stocks = Stock::where('product_id', $body['product_id'])
                    ->where('quantity', '>', 0)
                    ->where('center_id', $center)
                    ->orderBy('created_at', 'asc')
                    ->get();

                $remainingQuantity = $body['quantity'];

                foreach ($stocks as $stock) {

                    $invoiceBody = new InvoiceBody;

                    $invoiceBody->header_id = $invoiceHeader->id;
                    $invoiceBody->template_header_id = $temp['id'];
                    $invoiceBody->template_body_id = $body['id'];
                    $invoiceBody->product_id = $body['product_id'];
                    $invoiceBody->selling_price = $stock->price + ($stock->price * $profite / 100);
                    $invoiceBody->price = $stock['price'];

                    $currentQuantity = $stock->quantity;

                    if ($remainingQuantity >= $currentQuantity) {

                        $price = $stock->price + ($stock->price * $profite / 100) * $currentQuantity;
                        $total = $total + $price;

                        $stock->quantity = 0;
                        $stock->save();

                        $invoiceBody->quantity = $currentQuantity;
                        $invoiceBody->save();

                        $remainingQuantity -= $currentQuantity;
                    } else {

                        $price = $stock->price + ($stock->price * $profite / 100) * $remainingQuantity;
                        $total = $total + $price;

                        $stock->quantity -= $remainingQuantity;
                        $stock->save();

                        $invoiceBody->quantity = $remainingQuantity;
                        $invoiceBody->save();

                        $remainingQuantity = 0;
                        break;
                    }
                }
            }
        }
        $totaldiscount = ($total * $discount / 100);
        $invoiceHeader->discount = $totaldiscount;
        $invoiceHeader->value = $total - $totaldiscount;
        $invoiceHeader->save();

        if ($remainingQuantity > 0) {
            return response()->json(['message' => 'Insuficient Stock! Please contact your developer'], 500);
        } else {
            return response()->json([
                'message' => 'Successfully Saved',
                'id' => $invoiceHeader->id
            ]);
        }
    }

    public function Calculate(Request $request)
    {
        $total = 0;
        $profite = 0;
        $discount = 0;
        $center = $request['center_id'];

        //Check in stock
        foreach ($request->templates as $temp) {

            $templateBody = TemplateBody::where('template_id', $temp['id'])
                ->get();

            foreach ($templateBody as $body) {
                $sum = Stock::where('product_id', $body['product_id'])
                    ->where('center_id', $center)
                    ->sum('quantity');
                    
                if ($sum === null) {
                    $Product = Products::find($body['product_id']);
                    return response()->json(['message' => 'Stock not found for ' . $Product['product']], 500);
                }

                if ($sum < ($body['quantity'] * $temp['quantity'])) {
                    $Product = Products::find($body['product_id']);
                    return response()->json(['message' => 'Insuficient Stock in ' . $Product['product']], 500);
                }
            }
        }

        //make Calculations

        $MasterData = MasterData::where('center_id', $center)->first();

        $profite = $MasterData->profite;
        $discount = $MasterData->discount;

        foreach ($request->templates as $temp) {

            $templateBody = TemplateBody::where('template_id', $temp['id'])
                ->get();

            foreach ($templateBody as $body) {

                $stocks = Stock::where('product_id', $body['product_id'])
                    ->where('quantity', '>', 0)
                    ->where('center_id', $center)
                    ->orderBy('created_at', 'asc')
                    ->get();

                $remainingQuantity = $body['quantity'] * $temp['quantity'];

                foreach ($stocks as $stock) {
                    $price = 0;

                    $currentQuantity = $stock->quantity;

                    if ($remainingQuantity >= $currentQuantity) {

                        $price = $stock->price * $currentQuantity;
                        $pro = $price * $profite / 100;
                        $total = $total + ($price + $pro);

                        $remainingQuantity -= $currentQuantity;
                    } else {

                        $price = $stock->price  * $remainingQuantity;
                        $pro = $price * $profite / 100;
                        $total = $total + ($price + $pro);

                        $remainingQuantity = 0;
                        break;
                    }
                }
            }
        }
        $totaldiscount = ($total * $discount / 100);
        return response()->json(['total' => $total, 'discount' => $totaldiscount]);
    }
}