<?php

namespace App\Http\Controllers\Activity;

use App\Models\Activity;
use App\Http\Controllers\Controller;
use adriangibbons\phpFITFileAnalysis as FitFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class AnalysisController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \App\Activity  $activity
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Activity $activity)
    {
        $analysis = Cache::remember(sprintf('activity:%d:analysis', $activity->id), 3600, function () use ($activity) {
            $fit = new FitFile(Storage::path($activity->upload->file_path));

            if (!isset($fit->data_mesgs['record']['heart_rate'], $fit->data_mesgs['record']['heart_rate'])) {
                $output = [
                    'error' => 'no_data',
                ];
            } else {
                if (isset($activity->hr_max, $activity->ftp)) {
                    $output = $this->buildOutput($fit, $activity->hr_max, $activity->ftp);
                } else if ($activity->user->metric_profile) {
                    $output = $this->buildOutput($fit, $activity->user->metric_profile->hr_max, $activity->user->metric_profile->ftp);
                } else {
                    $output = [
                        'error' => 'no_metrics',
                    ];
                }
            }

            if (!isset($output['error'])) {
                foreach ($output as $type => $values) {
                    $output[$type] = [];
                    foreach ($values as $key => $value) {
                        $output[$type][] = ['key' => $key, 'value' => $value];
                    }
                }
            }

            return $output;
        });

        return response()->json($analysis);
    }

    private function buildOutput($fit, $hr_max, $ftp)
    {
        return [
            'hr_partition' => count($fit->data_mesgs['record']['heart_rate'])
                ? $fit->hrPartionedHRmaximum($hr_max)
                : [],
            'power_partition' => count($fit->data_mesgs['record']['power'])
                ? $fit->powerPartioned($ftp)
                : [],
            'power_metrics' => count($fit->data_mesgs['record']['power'])
                ? $fit->powerMetrics($ftp)
                : [],
        ];
    }
}
