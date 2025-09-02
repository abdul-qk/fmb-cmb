<?php

namespace App\Http\Controllers;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfController extends Controller
{
  public function generatePDF()
  {
    // Sample data to pass to the view
    $data = ['name' => 'John Doe'];

    // Load the view into a variable
    $view = view('pdf.sample', $data)->render();

    // Set options for Dompdf
    $options = new Options();
    $options->set('defaultFont', 'Arial');
    $options->set('isHtml5ParserEnabled', true);

    // Instantiate Dompdf with options
    $dompdf = new Dompdf($options);

    // Load the HTML content
    $dompdf->loadHtml($view);

    // Set paper size and orientation
    $dompdf->setPaper('A4', 'portrait');

    // Render the PDF
    $dompdf->render();

    // Stream the generated PDF to the browser
    return $dompdf->stream('sample.pdf', ['Attachment' => true]);
  }
}
