<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Center;
use App\Models\TemplateBody;
use App\Models\InvoiceHeader;
use App\Models\InvoiceBody;
use App\Models\Products;
use App\Models\Patient;
use App\Models\User;
use TCPDF;
use PDF;

class PdfController extends Controller
{
    public function Invoice($id)
    {
        $Header = InvoiceHeader::find($id);

        $pdf = new TCPDF('P', 'mm', array(80, 250), true, 'UTF-8', false);

        $pdf->SetPrintHeader(false);

        $imagePath = public_path() . '/Logo/image.png';
        $logoWidth = 30;

        $pdf->AddPage();

        $pageWidth = $pdf->GetPageWidth();

        $imageX = ($pageWidth - 30) / 2;
        
        $pdf->SetAutoPageBreak(false);
        $pdf->SetMargins(0, 0, 0, true);
        $pdf->SetPrintHeader(false);
   
        $font_size_10 = 10;
        $font_size_08 = 8;
        
        // Add the logo image
        $pdf->Image($imagePath, $imageX, $pdf->GetY(), 30);
        $pdf->SetY($pdf->GetY() + 20);
    
        // Set company address
        $Center = Center::find($Header['center_id']);

        $pdf->SetFont('Helvetica', '', $font_size_08);
        $pdf->Write(0, $Center['address_01'], '', 0, 'C', true, 0, false, false, 0);
        $pdf->Write(0, $Center['address_02'], '', 0, 'C', true, 0, false, false, 0);
           
        // Add a horizontal ruler
        $x1 = $pdf->GetX();
        $y1 = $pdf->GetY() + 1;
        $x2 = $pdf->getPageWidth() - $pdf->getMargins()['right'];
        $y2 = $y1;
        $pdf->SetLineStyle(array('width' => 0.5, 'color' => array(0, 0, 0)));
        $pdf->Line($x1, $y1, $x2, $y2);
    
        // Set invoice title
        $pdf->Ln(2);
        $pdf->SetFont('Helvetica', 'B', 10);
        $pdf->Write(0, 'Invoice', '', 0, 'C', true, 0, false, false, 0);

        
        $User = User::find($Header['created_by']);
    
        // Set invoice details
        $pdf->Ln(2);
        $pdf->SetFont('Helvetica', '', $font_size_10);
        $pdf->Write(0, 'Invoice No: '. $Header['invoice'], '', 0, 'L', true, 0, false, false, 0);
        $pdf->Write(0, 'Center: '. $Center['center'], '', 0, 'L', true, 0, false, false, 0);
        $pdf->SetFont('Helvetica', '', $font_size_08);
        $pdf->Write(0, 'Created At: '. $Header['created_at'], '', 0, 'L', true, 0, false, false, 0);
        
        $pdf->Ln(2);
        $pdf->SetFont('Helvetica', 'B', $font_size_10);
        $pdf->Write(0, 'Surgeon: '. $User['name'], '', 0, 'L', true, 0, false, false, 0);
        $pdf->SetFont('Helvetica', '', 7);
        $pdf->Write(0, $User['designation']."(".$User['campus']."), ".$User['slmc_reg_no'], '', 0, 'L', true, 0, false, false, 0);
        $pdf->SetFont('Helvetica', '', 9);
        $pdf->Write(0, 'Base Hospital: '. $User['base_hospital'], '', 0, 'L', true, 0, false, false, 0);
    
        $InvoiceBody = InvoiceBody::find($id);

        // SELECT `header_id`,SUM(`selling_price`),COUNT(`template_body_id`) FROM `invoice_bodies` LEFT OUTER JOIN `template_headers` on `template_headers`.`id` = `invoice_bodies`.`template_header_id` WHERE `header_id` = 5 GROUP BY `template_body_id`;

        SELECT `header_id`,`template_headers`.`template_name`,SUM(`selling_price`),COUNT(`template_body_id`) FROM `invoice_bodies` 
INNER JOIN `template_headers` on `template_headers`.`id` = `invoice_bodies`.`template_header_id` 
WHERE `header_id` = 5 GROUP BY `template_body_id`;

$products = InvoiceBody::with('templateHeader')
->select('header_id', 'template_headers.template_name as name')
->selectRaw('SUM(selling_price) as price')
->selectRaw('COUNT(template_body_id) as quantity')
->where('header_id', $id)
->groupBy('template_body_id')
->get();



        // $products = array(
        //     array(
        //         'name' => 'Treatment Template 1',
        //         'quantity' => 2,
        //         'price' => 20.00,
        //     ),
        //     array(
        //         'name' => 'Treatment Template 2 with a very long name that will wrap within the cell',
        //         'quantity' => 1,
        //         'price' => 10.00,
        //     ),array(
        //         'name' => 'Treatment Template 1',
        //         'quantity' => 2,
        //         'price' => 20.00,
        //     ),
        //     array(
        //         'name' => 'Treatment Template 2 with a very long name that will wrap within the cell',
        //         'quantity' => 1,
        //         'price' => 10.00,
        //     )
        // );
    
        // Table Header..........................................................
        $pdf->Ln(2);
    
        // Set the maximum width for each column
        $pdf->SetFont('Helvetica', 'B', $font_size_08);
        $itemWidth = 35;
        $quantityWidth = 15;
        $priceWidth = 15;
        $totalWidth = 80 - ($itemWidth + $quantityWidth + $priceWidth);

        // Table Body..........................................................
        // Set the table headers
        $pdf->setLineStyle(array('width' => 0));
        $pdf->Cell($itemWidth, 6, 'Item', 1, 0, 'L');
        $pdf->setLineStyle(array('width' => 0));
        $pdf->Cell($quantityWidth, 6, 'Qty', 1, 0, 'L');
        $pdf->setLineStyle(array('width' => 0));
        $pdf->Cell($priceWidth, 6, 'Price', 1, 0, 'L');
        $pdf->setLineStyle(array('width' => 0));
        $pdf->Cell($totalWidth, 6, 'Total', 1, 1, 'L');

        $pdf->SetFont('Helvetica', '', $font_size_08);
        $grandTotal = 0;

        foreach ($products as $product) {
            $total = $product['quantity'] * $product['price'];
            $grandTotal += $total;
    
            
            $productNameHeight = $pdf->getStringHeight($itemWidth, $product['name']);
    
            $pdf->MultiCell($itemWidth, $productNameHeight, $product['name'], 1, 'L');
            $pdf->SetXY($pdf->GetX() + $itemWidth, $pdf->GetY() - $productNameHeight);
    
            $pdf->setLineStyle(array('width' => 0));
            $pdf->Cell($quantityWidth, $productNameHeight, $product['quantity'], 1, 0, 'L');
            $pdf->setLineStyle(array('width' => 0));
            $pdf->Cell($priceWidth, $productNameHeight, '$' . number_format($product['price'], 2), 1, 0, 'L');
            $pdf->setLineStyle(array('width' => 0));
            $pdf->Cell($totalWidth, $productNameHeight, '$' . number_format($total, 2), 1, 1, 'L');
        }

        // Table Footer..........................................................
        $pdf->SetFont('Helvetica', 'B', $font_size_10);
        $pdf->Ln(2);
        $pdf->SetX(40);
        $pdf->Write(0, 'Grand Total: $' . number_format($grandTotal, 2), '', 0, 'L', true, 0, false, false, 5);


        $patient = Patient::find($Header['patient_id']);
        // Insert customer details
        $pdf->SetFont('Helvetica', 'B', 9);
        $customerName = "Naveen";
        $customerAddress = "Welimada";
        $pdf->Ln(5);
        $pdf->Write(0, 'Customer Details', '', 0, 'L', true, 0, false, false, 5);
        $pdf->SetFont('Helvetica', '', $font_size_08);
        $pdf->Write(0, 'Name: '. $patient['patient_name'], '', 0, 'L', true, 0, false, false, 5);
        $pdf->Write(0, 'Address: ' . $patient['patient_address'], '', 0, 'L', true, 0, false, false, 5);
        
        // Add a horizontal ruler
        $pdf->Ln(2);
        $x1 = $pdf->GetX();
        $y1 = $pdf->GetY() + 1;
        $x2 = $pdf->getPageWidth() - $pdf->getMargins()['right'];
        $y2 = $y1;
        $pdf->SetLineStyle(array('width' => 0.5, 'color' => array(0, 0, 0)));
        $pdf->Line($x1, $y1, $x2, $y2);

        $pdf->SetFont('Helvetica', 'B', 7);
        $pdf->Ln(2);
        $pdf->Write(0, 'For Billing Questions Call ' , '', 0, 'C', true, 0, false, false, 5);
        $pdf->SetFont('Helvetica', '', 7);
        $pdf->Write(0, $Center['contact_no'], '', 0, 'C', true, 0, false, false, 0);
        $pdf->Write(0, $Center['work_days'], '', 0, 'C', true, 0, false, false, 0);
        $pdf->Write(0, $Center['service_time'], '', 0, 'C', true, 0, false, false, 0);

        // Add a horizontal ruler
        $pdf->Ln(2);
        $x1 = $pdf->GetX();
        $y1 = $pdf->GetY() + 1;
        $x2 = $pdf->getPageWidth() - $pdf->getMargins()['right'];
        $y2 = $y1;
        $pdf->SetLineStyle(array('width' => 0.5, 'color' => array(0, 0, 0)));
        $pdf->Line($x1, $y1, $x2, $y2);
    
        // Invoice Footer..........................................................
        $pdf->SetFont('Helvetica', 'I', 6);
        $pdf->Ln(2);
        $pdf->Write(0, 'Powered By Whizzlogy Solutions', '', 0, 'C', true, 0, false, false, 0);
    
        $pdf->Output('Invoice.pdf');
    }
}
