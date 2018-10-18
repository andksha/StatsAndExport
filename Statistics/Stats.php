<?php namespace App;

use DB;
use App\User;

class Stats
{

    public function getPersonalStat(User $user)
    {
        if ($user->role_id != 1) {   
            $stat = DB::table('terms')->select($this->getCountedStats($user))
                ->whereBetween('status_id', [1, 100])
                ->where('user_id', '=', $user->id)
                ->first();
        }
        
        return $this->getLinks($user, $stat);;
    }

    public function getPublicStat(User $user)
    {
        $stat = DB::table('terms')->select($this->getCountedStats())
                ->whereBetween('status_id', [10, 100])
                ->first();
        
        return $this->getLinks($user, $stat);;
    }
    
    private function getLinks(User $user, $stat)
    {
        $links = [];
        
        if (is_null($user->role_id) == false) {
            $links = [
                'draft' =>'p_statuses='. 1 . '&p_users=' . $user->id,
                'archive' =>'p_statuses='. 9 . '&p_users=' . $user->id
            ];
        }
        
        $links['draft'] = 'p_statuses='. 1 . '&p_users=' . $user->id;
        $links['archive'] = 'p_statuses='. 9 . '&p_users=' . $user->id;
        $links['active'] ='p_statuses='. 10 . '&p_users=' . $user->id;
        $links['ready'] = 'p_statuses='. 11 . '&p_users=' . $user->id;
        $links['duplicate'] = 'p_statuses='. 13 . '&p_users=' . $user->id;
        $links['hot'] = 'p_statuses='. 14 . '&p_users=' . $user->id;
        $links['published'] = 'p_statuses='. 100 . '&p_users=' . $user->id;
        $links['total'] = 'p_statuses=' . 0 . '&p_users=' . $user->id;
                
        foreach ($stat as $key => $s) {
            $stat->$key = ['stat' => $s, 'link' => $links[$key]];
        }

        return $stat;
    }
    
    private function getCountedStats($user = null)
    {
        $countedStats = [];
        
        if ($user != null) {
            $countedStats = [
                DB::raw('SUM(CASE WHEN status_id = 1 then 1 else 0 end) draft'),
                DB::raw('SUM(CASE WHEN status_id = 9 then 1 else 0 end) archive')
            ];
        }
        
        array_push($countedStats, 
            DB::raw('SUM(CASE WHEN status_id = 10 then 1 else 0 end) active'),
            DB::raw('SUM(CASE WHEN status_id = 11 then 1 else 0 end) ready'),
            DB::raw('SUM(CASE WHEN status_id = 13 then 1 else 0 end) duplicate'),
            DB::raw('SUM(CASE WHEN status_id = 14 then 1 else 0 end) hot'),
            DB::raw('SUM(CASE WHEN status_id = 100 then 1 else 0 end) published'),
            DB::raw('COUNT(id) as total')
        );

        return $countedStats;
    }

    public function getVotingStat($user)
    {
        $voted = DB::table('terms')
            ->select('terms.id', 'expratings.id')
            ->join('expratings', 'expratings.term_id', '=', 'terms.id')
            ->where('expratings.user_id', '=', $user->id)
            ->where('terms.status_id', '=', 10)
            ->get();

        $stat['voted']['stat'] = count($voted);

        $not_voted = DB::table('terms')->where('terms.status_id', '=', 10)
            ->count() - count($voted);

        $stat['not_voted']['stat'] = $not_voted;
        
        return $stat;
    }

    public function getTermvotingStat($term_id)
    {
        $termObj = DB::table('terms')->select('term')
                ->where('id', '=', $term_id)
                ->first();

        $term_group = DB::table('terms')
            ->select(
                'terms.term', 'terms.source', 'terms.comm',
                DB::raw('CONCAT(users.last_name, " ", users.name) as last_name'),
                DB::raw('SUM(CASE WHEN rating_id = 10 then 1 else 0 end) votes_up'),
                DB::raw('SUM(CASE WHEN rating_id = -10 then 1 else 0 end) votes_down'),
                DB::raw('SUM(CASE WHEN rating_id = 1 then 1 else 0 end) votes_viewed')
            )
            ->leftJoin('users', 'terms.user_id', '=', 'users.id')
            ->leftJoin('expratings', 'expratings.term_id', '=', 'terms.id')
            ->where('terms.term', 'like', '%' . $termObj->term . '%')
            ->where('terms.status_id', '=', 10)
            ->groupBy('expratings.term_id')
            ->get();

        return $term_group;
    }
    
}
