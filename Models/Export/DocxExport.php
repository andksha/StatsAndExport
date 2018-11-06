<?php

namespace App\Models\Export;

use App\Models\Export\Export;

class DocxExport extends Export
{
    
    public function __construct($format, $orientation, $view)
    {
        parent::__construct($format, $orientation, $view);
    }
    
}