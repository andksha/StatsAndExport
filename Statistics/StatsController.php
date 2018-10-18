<?php namespace App\Http\Controllers;

use App\User;
use App\Stats;
use Request;

class StatsController extends BaseController
{    
    public function getIndex()
    {        
        $statistics = new Stats();
        $user = User::current();
        $term_id = Request::input('id');

        if (is_null($user->role_id) == false) {
            if (is_null($term_id) == false) {
                $term_group = $statistics->getTermvotingStat($term_id);
                
                return view('stats.grid')
                    ->with('term_group', $term_group);
            } else {
                $tables['personal'] = $statistics->getPersonalStat($user);
                $tables['voting'] = $statistics->getVotingStat($user);
            }
        }
        
        $tables['public'] = $statistics->getPublicStat($user);
        
        return view('stats.grid')
            ->with('tables', $tables);
    }

}