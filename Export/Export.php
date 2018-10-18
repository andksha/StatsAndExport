<?php

namespace App;

use DB;
use App\User;
use App\Export;
use App\Term;
use Session;

class Export extends G
{
    protected $terms = '';

    public function __construct($layout)
    {
        $this->setTerms($this->queryForExports($layout));
    }
    
    public function getTerms()
    {
        return $this->terms;
    }
    
    public function setTerms($terms)
    {
        $this->terms = $terms;
    }

    public function getText($term)
    {        
        if ($term->category_id == 0 || $term->area_id == 0) {
            $term->category_id = null;
            $term->area_id = null;
        }
        
        if (is_null($term->english) == false & $term->english != '') {
            $term->english = 'Англ.: ' . $this->trimExcessSpaces($term->english);
        }
        if (is_null($term->synterm) == false & $term->synterm != '') {
            $term->synterm = 'Синонім: ' . $this->trimExcessSpaces($term->synterm);
        }
        if (is_null($term->source) == false & $term->source != '') {
            $term->source = 'Джерело: ' . $this->trimExcessSpaces($term->source);
        }
        if (is_null($term->category_id) == false & $term->category_id != 0) {
            $term->category_id = 'Клас: ' . DB::table('categories')->select('name')
                            ->where('id', '=', $term->category_id)
                            ->first()->name;
        }
        if (is_null($term->area_id) == false & $term->area_id != 0) {
            $term->area_id = 'Сфера: ' . DB::table('areas')->select('name')
                            ->where('id', '=', $term->area_id)
                            ->first()->name;
        }

        return $term;
    }

    private function queryForExports($layout)
    {
        $terms = DB::table('terms')->select($this->getColumns($layout));
        $attributes = $this->getAttributesNames();

        foreach ($attributes as $key => $attr) {
            if ($attr != null) {
                $terms = $terms->where($key, '=', $attr);
            } else {
                $terms = $terms->where('status_id', '>', 9);
            }
        }
        
        return $terms->get();
    }

    private function getColumns($layout)
    {      
        $columns = ['term', 'english', 'definition', 'source'];
        
        if ($layout == 2) {
            array_push($columns, 'category_id', 'area_id', 'synterm');
        }
        
        return $columns;
    }

    private function getAttributesNames()
    {
        $attributes['area_id'] = Session::get('s_p_areas');
        $attributes['category_id'] = Session::get('s_p_categories');
        $attributes['sections_id'] = Session::get('s_p_sections');
        $attributes['status_id'] = Session::get('s_p_statuses');
        $attributes['user_id'] = Session::get('s_p_users');
        
        foreach ($attributes as $key => $val) {
            if ($val == (null || 0 || '')) {
                unset($attributes[$key]);
            }
        }
        
        return $attributes;
    }

    public function trimExcessSpaces($string)
    {
        return trim(preg_replace('/\s+/', ' ', $string));
    }

}