<?php

namespace App\Models\Export;

use PhpOffice;
use App\Models\Export\Export;

class PdfExport extends Export
{
    
    protected $writer = 'PDF';
    protected $fontname = 'DejaVu Sans';
    
    public function __construct($format, $orientation, $view)
    {
        parent::__construct($format, $orientation, $view);
    }
    
}