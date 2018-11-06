<?php

namespace App\Models\Export;

use DB;
use App\Area;
use App\Category;
use Session;
use PhpOffice;

class Export
{
    
    protected $layout; // columns of term to export.
    protected $terms; // data from db
    protected $filename; // exported file`s name
    protected $format; // .docx, .pdf or .html
    protected $orientation; // portrait or landscape
    protected $view; // inline or table
    protected $writer = 'Word2007';
    protected $fontname = 'Times New Roman';

    public function __construct($format, $orientation, $view)
    {
        $this->setLayout();
        $this->setTerms($this->queryForExports());
        $this->setFilename('glossary');
        $this->setFormat($format);
        $this->setOrientation($orientation);
        $this->setView($view);
    }
    
    public function getTerms()
    {
        return $this->terms;
    }

    public function setTerms($terms)
    {
        $this->terms = $terms;
    }
    
    function getLayout()
    {
        return $this->layout;
    }

    function setLayout()
    {
        
        $this->layout = ['term', 'abbrev', 'english',
            'definition', 'source', 'synterm'];
        
        /*
        if ($layout == 2) {
            array_push($columns, 'category_id', 'area_id', 'synterm');
        }
        
        $this->layout = $layout;*/
    }

    public function getFilename()
    {
        return $this->filename;
    }

    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    public function getFormat()
    {
        return $this->format;
    }

    public function setFormat($format)
    {
        $validFormats = ['.docx', '.pdf', '.hmtl'];
        
        if (in_array($format, $validFormats, true)) {
            $this->format = $format;
        } else {
            $this->format = '.docx';
        }
    }
    
    function getOrientation()
    {
        return $this->orientation;
    }

    function setOrientation($orientation)
    {
        $validOrientations = ['portrait', 'landscape'];
        
        if (in_array($orientation, $validOrientations, true)) {
            $this->orientation = $orientation;
        } else {
            $this->orientation = 'portrait';
        }
    }

    public function getView()
    {
        return $this->view;
    }

    public function setView($view)
    {
        $validViews = ['inline', 'table'];
        
        if (in_array($view, $validViews, true)) {
            $this->view = $view;
        } else {
            $this->view = 'inline';
        }
    }
    
    public function export()
    {
        $phpWord = $this->initPhpWord();
        
        $objWriter = PhpOffice\PhpWord\IOFactory::createWriter($phpWord, $this->writer);
        $objWriter->save($this->filename . $this->format);

        $this->readExportedFile();
    }
    
    protected function initPhpWord()
    {
        $phpWord = new PhpOffice\PhpWord\PhpWord();
        
        $phpWord->setDefaultFontName($this->fontname);
        
        $titleStyle = $phpWord->addTitleStyle(0, ['bold' => true, 'size' => 13], ['tabs' => 2]);
        $tableStyle = [
                'borderSize' => 6,
                'borderColor' => '000000',
                'cellMargin' => 80
            ];
        $phpWord->addTableStyle('ExportTable', $tableStyle);
        
        $section = $phpWord->addSection();
        $section = $this->fillSection($section);
        
        return $phpWord;
    }
    
    protected function readExportedFile()
    {
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=". $this->filename . $this->format);
        header("Content-Type: application/" . $this->format);
        header("Content-Transfer-Encoding: binary");

        readfile($this->filename . $this->format);
        unlink($this->filename . $this->format);
        
        exit;
    }
    
    protected function fillSection($section)
    {
        if ($this->view == 'inline') {
            foreach ($this->getTerms() as $term) {
                $section->addTitle($this->mb_ucfirst(htmlspecialchars($this->trimES($term->term)), 'utf8'));
                foreach ($this->getText($term) as $key => $t) {
                    if ($key != 'term' & is_null($t) == false) {
                        $section->addText('    ' . $t);
                    }
                }
                $section->addText();
            }
            
            return $section;
        } elseif ($this->view == 'table') {
            $table = $section->addTable('ExportTable');
            $cellStyle = ['valign' => 'center'];
            $textStyle = ['align' => 'center'];
            $cellLength = 0;
            
            if ($this->orientation == 'landscape') {
                $cellLength = 1000;
            }
            
            $table->addRow();
            $table->addCell(500)
                    ->addText('№', null, $textStyle);
            $table->addCell(1000 + $cellLengths, $cellStyle)
                    ->addText(trans('grid.terms.term'), null, $textStyle);
            $table->addCell($cellLength, $cellStyle)
                    ->addText(trans('grid.terms.abbrev'), null, $textStyle);
            $table->addCell(1000 + $cellLength, $cellStyle)
                    ->addText(trans('grid.terms.english'), null, $textStyle);
            $table->addCell(3000 + $cellLength, $cellStyle)
                    ->addText(trans('grid.terms.definition'), null, $textStyle);
            $table->addCell(1000 + $cellLength, $cellStyle)
                    ->addText(trans('grid.terms.source'), null, $textStyle);
            $table->addCell($cellLength, $cellStyle)
                    ->addText(trans('grid.terms.synterm'), null, $textStyle);
            $table->addRow();
            for ($i = 1; $i < 8; $i++) {
                $table->addCell(500)->addText($i, null, $textStyle);
            }
            
            $j = 1;
            foreach ($this->getTerms() as $term) {
                $table->addRow();
                $table->addCell(500)->addText($j . '.', null, $textStyle);
                foreach ($term as $key => $t) {
                    $t = htmlspecialchars($this->trimES($t));
                    if ($key == 'definition') {
                        $table->addCell(3000 + $cellLength)->addText($t);
                    } else {
                        $table->addCell(1000 + $cellLength)->addText($this->mb_ucfirst($t, 'utf8'));
                    }
                }
                $j++;
            }
            
            return $table;
        }
    }

    protected function getText($term)
    {
        foreach ($term as $key => $t) {
            $term->$key = $this->mb_ucfirst(htmlspecialchars($this->trimES($t)), 'utf8');
            if (empty($term->$key) != true) {
                if (preg_match('/\b(term|definition|category_id|area_id)\b/', $key) != 1) {
                    $term->$key = trans('exchange.export.captions.' . $key) . $term->$key;
                } elseif ($key == 'category_id') {
                    $term->$key = trans('exchange.export.captions.' . $key) . Category::getCategoryName($t);
                } elseif ($key == 'area_id') {
                    $term->$key = trans('exchange.export.captions.' . $key) . Area::getAreaName($t);
                }
            } else {
                $term->$key = null;
            }
        }

        return $term;
    }

    protected function queryForExports()
    {
        $terms = DB::table('terms')->select($this->layout);
        $attributes = $this->getAttributesNames();

        if (empty($attributes) == false) {
            foreach ($attributes as $key => $attr) {
                if (is_null($attr) == false) {
                    $terms = $terms->where($key, '=', $attr);
                } else {
                    $terms = $terms->where('status_id', '>', 9);
                }
            }
        } else {
            $terms = $terms->where('status_id', '>', 9);
        }

        return $terms->orderBy('term')->get();
    }

    protected function getAttributesNames()
    {
        $attributes['area_id'] = Session::get('s_p_areas');
        $attributes['category_id'] = Session::get('s_p_categories');
        $attributes['section_id'] = Session::get('s_p_sections');
        $attributes['status_id'] = Session::get('s_p_statuses');
        $attributes['user_id'] = Session::get('s_p_users');
        
        
        foreach ($attributes as $key => $val) {
            if ($val == null || $val == 0) {
                unset($attributes[$key]);
            }
        }

        return $attributes;
    }

    protected function trimES($string)
    {
        return trim(preg_replace('/\s+/', ' ', $string));
    }
    
    protected function mb_ucfirst($string, $encoding)
    {
        $strlen = mb_strlen($string, $encoding);
        $firstChar = mb_substr($string, 0, 1, $encoding);
        $then = mb_substr($string, 1, $strlen - 1, $encoding);
        
        return mb_strtoupper($firstChar, $encoding) . $then;
    }
    
}