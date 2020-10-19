<?php

namespace App\Http\Livewire\Stats;

use App\Models\Activity;
use App\Models\Stat;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DashboardGraph extends Component
{
    public function render()
    {
        $types = Activity::$statable;
        $stats = Stat::where('user_id', Auth::id())
            ->where('week', '<=', date('W'))
            ->where('year', date('Y'))
            ->orWhere(function ($query) {
                $query->where('week', '>', date('W'));
                $query->where('year', date('Y') - 1);
            })
            ->get()
            ->mapToGroups(function ($item) {
                $week = strlen($item->week) === 1 ? '0'.$item->week : $item->week;
                $key = strtotime(date('d-m-Y', strtotime($item->year.'W'.$week)));
                return [$key => $item->only('type', 'value')];
            })->map(function ($item) {
                return $item->mapWithKeys(function ($item) {
                    return [$item['type'] => $item['value']];
                });
            })->sortKeys();



        $empty = collect($types)->mapWithKeys(function ($type) {
            return [$type => 0];
        });

        $week = strtotime(date('Y').'W'.date('W'));
        for ($i = 52; $i > 0; $i--) {
            $key = strtotime(date('d-m-Y', $week));
            if (!isset($stats[$key])) {
                $stats[$key] = $empty;
            }
            $week = strtotime('-7 days', $week);
        }

        return view('stats.dashboard-graph', compact('stats', 'types'));
    }
}
