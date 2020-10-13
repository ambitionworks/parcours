<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Laravel\Jetstream\Jetstream;

class TeamToggleController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $team = Jetstream::newTeamModel()->findOrFail($request->team_id);

        if (! $request->user()->belongsToTeam($team)) {
            abort(403);
        }

        if ($team->id == $request->user()->current_team_id) {
            $team = $request->user()->personalTeam();
        }

        $request->user()->forceFill([
            'current_team_id' => $team->id,
        ])->save();

        return back(303);
    }
}
