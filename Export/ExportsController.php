<?php

namespace App\Http\Controllers;

use App\BaseGreed;
use App\Models\Export\ExportFactory;
use Request;

define('USER', 20); // Роль рядового пользователя    

// спросить про composer на сервере

class  ExportsGrid extends BaseGreed {
    static  $group_row;
    public function   event_before_row($row)
    {
        $rc = ''; 
        $grp = $row->collgrp;    // Grouping field
        if (static::$group_row != $grp) {
            static::$group_row = $grp;
            //$rc = "<b><u>$grp</u></b><br>";
            return "<tr><td class = 'grid_g' colspan=7> $grp </td></tr>";
        }
    }
}



class ExportsController extends BaseController
{
    var $message   = null;
    
    public function postPassport($id)
    {
        $oper  = Request::input('codeoper',Request::input('run'));
        $body  = '';
        $top   = null;
        $row   = null;

        if ("$oper" === "display") {
        }
        elseif ("$oper" === "edit") {
        }
        return redirect("/exports/passport/$id");
    }

    public function getIndex()
    {
        return view('exports.export');
    }

    public function getGrid()
    {
        //$layout = Request::input('p_layouts');
        $format = Request::input('p_formats');
        $orientation = Request::input('p_orientations');
        $view = Request::input('p_views');

        //$export = new Export($format, $orientation, $view);
        $export = ExportFactory::produce($format, $orientation, $view);
        $export->export();
    }
}