<?php

namespace App\Http\Livewire\Segments;

use App\Models\Segment;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class LeaderboardList extends Component
{
    use WithPagination;

    public Segment $segment;

    public function render()
    {
        $leaderboard = DB::table(DB::raw('
            (SELECT DISTINCT ON (user_activities.user_id) *,  RANK () OVER (ORDER BY user_activities.elapsed ASC) AS position FROM
                (SELECT a.user_id, u.name AS username, s.*, s.end_time - s.start_time as elapsed FROM activity_segment s INNER JOIN activities a ON a.id = s.activity_id INNER JOIN users u ON a.user_id = u.id WHERE s.segment_id = '.$this->segment->id.' ORDER BY elapsed) user_activities
            ORDER BY user_activities.user_id, user_activities.elapsed
            ) leaders
        '))->paginate(10);

        return view('segments.leaderboard-list', ['leaderboard' => $leaderboard]);
    }
}
