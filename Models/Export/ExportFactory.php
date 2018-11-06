<?php

namespace App\Models\Export;

class ExportFactory
{
    
    public static function produce($format, $orientation, $view)
    {
        $export = '\App\Models\Export\\' . ucfirst(substr($format, 1)) . 'Export';
        
        return new $export($format, $orientation, $view);
        if (class_exists($export)) {
            return new $export($format, $orientation, $view);
        } else {
            abort(404);
        }
    }
    
}

