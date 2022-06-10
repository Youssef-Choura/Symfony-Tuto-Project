<?php

namespace App\Services;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfService
{
    private Dompdf $domPdf;
    public function __construct()
    {
        $this->domPdf = new Dompdf();
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $this->domPdf->setOptions($pdfOptions);
    }
    public function generatePdf(string $html): void
    {
        $this->domPdf->loadHtml($html);
        $this->domPdf->render();
        $this->domPdf->stream("my_pdf.pdf", [
            "Attachment" => false
        ]);
    }
    public function generatePdfFromHtml(string $html): string
    {
        $this->domPdf->loadHtml($html);
        $this->domPdf->render();
        return $this->domPdf->output();
    }
}