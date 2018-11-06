<?php

namespace App\Models\Export;

use PhpOffice;
use App\Models\Export\Export;

class HtmlExport extends Export
{
    
    protected $writer = 'HTML';
    
    public function __construct($format, $orientation, $view)
    {
        parent::__construct($format, $orientation, $view);
        
    }
    
    public function fillSection($section)
    {
        
    }
    
}