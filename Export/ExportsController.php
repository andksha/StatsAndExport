<?php

namespace App\Http\Controllers;

use App\BaseGreed;
use App\Export;
use App\Member;
use App\Dic;
use DB;
use App\Exceptions\TransactionException;
use Request;
use PhpOffice;

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
        $oper  = \Request::input('codeoper',\Request::input('run'));
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
        $layout = Request::input('p_layouts');
        $format = Request::input('p_formats');
        $filename = 'glossary.';
        $export = new Export($layout);
        $phpWord = new PhpOffice\PhpWord\PhpWord();
        $phpWord->setDefaultFontName('DejaVu Sans');
        
        $bold = new PhpOffice\PhpWord\Style\Font();
        $bold->setBold(true);
        $bold->setSize(13);
        
        $paragraphStyle = new PhpOffice\PhpWord\Style\Paragraph();
        $paragraphStyle->setTabs(4);
        
        $section = $phpWord->addSection();

        foreach ($export->getTerms() as $term) {
            $termName = $section->addText('  ' . htmlspecialchars($export->trimExcessSpaces($term->term)));
            $termName->setFontStyle($bold);
            foreach ($export->getText($term) as $key => $t) {
                if ($key != 'term' & is_null($t) == false) {
                    if ($key == 'category_id') {
                        $section->addText('    ' . $t);
                    } else {
                        $section->addText('    ' . htmlspecialchars($export->trimExcessSpaces($t)));
                    }
                }
            }
            $section->addText();
            
        }
        
        $objWriter = PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($filename . 'docx');
        
        if ($format == 'pdf') {
            $phpword = PhpOffice\PhpWord\IOFactory::load($filename . 'docx');
            PhpOffice\PhpWord\Settings::setPdfRendererPath('../vendor/dompdf/dompdf');
            PhpOffice\PhpWord\Settings::setPdfRendererName('DomPDF');
            
            $objWriter = PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'PDF');
        } elseif ($format == 'html') {
            $objWriter = PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'HTML');
        } elseif ($format != 'docx') {
            abort(404);
        }
        
        $objWriter->save($filename . $format);
        
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=". $filename . $format);
        header("Content-Type: application/" . $format);
        header("Content-Transfer-Encoding: binary");

        readfile($filename . $format);
        unlink($filename . $format);
        
        exit;
    }
}