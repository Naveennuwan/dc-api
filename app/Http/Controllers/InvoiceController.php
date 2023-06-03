<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvoiceRequest;
use App\Models\Products;
use App\Models\Stock;
use App\Models\TemplateBody;
use App\Models\InvoiceHeader;
use App\Models\InvoiceBody;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{

    public function Index(InvoiceRequest $request)
    {
        $user = Auth::user();

        $center = $request['center_id'];

        //Check in stock
        foreach ($request->templates as $temp) {

            $templateBody = TemplateBody::where('template_id', $temp['id'])
                ->get();

            foreach ($templateBody as $body) {
                $sum = Stock::where('product_id', $body['product_id'])
                    ->where('center_id', $center)
                    ->sum('quantity');

                if ($sum < $body['quantity']) {
                    $Product = Products::find($body['product_id']);
                    return response()->json(['message' => 'Insuficient Stock in ' . $Product['product']], 500);
                }
            }
        }

        
        $maxId = InvoiceHeader::max('id');
        $CenterCode = "C" . str_pad($center, 2, '0', STR_PAD_LEFT);
        $UserCode = "U" . str_pad($user->id, 3, '0', STR_PAD_LEFT);
        $InvCode = "I" . str_pad($maxId + 1, 7, '0', STR_PAD_LEFT);
        
        $invoiceHeader = new InvoiceHeader;
        
        $invoiceHeader->invoice = $CenterCode . $UserCode . $InvCode;
        $invoiceHeader->center_id = $center;
        $invoiceHeader->patient_id = $request->input('patient_Id');
        $invoiceHeader->created_by = $user->id;

        
        $invoiceHeader->save();
        
        //Insert InvoiceBody Add Stock Management
        
        foreach ($request->templates as $temp) {
            
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
                    $invoiceBody->selling_price = $stock['selling_price'];
                    $invoiceBody->price = $stock['price'];

                    $currentQuantity = $stock->quantity;

                    if ($remainingQuantity >= $currentQuantity) {
                        $stock->quantity = 0;
                        $stock->save();
                        
                        $invoiceBody->quantity = $currentQuantity;    
                        $invoiceBody->save();

                        $remainingQuantity -= $currentQuantity;
                    } else {
                        $stock->quantity -= $remainingQuantity;
                        $stock->save();

                        $invoiceBody->quantity = $remainingQuantity;    
                        $invoiceBody->save();

                        $remainingQuantity = 0;
                        break;
                    }
                }

                if ($remainingQuantity > 0) {
                    return response()->json(['message' => 'Insuficient Stock! Please contact your developer'], 500);
                } else {
                    return response()->json(['message' => 'Successfully Saved']);
                }
            }
        }
    }
}
