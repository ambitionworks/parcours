<?php

namespace App\Actions;

use App\Models\Activity;
use App\Models\Segment;
use Illuminate\Support\Facades\Storage;
use adriangibbons\phpFITFileAnalysis as FitFile;
use MStaack\LaravelPostgis\Geometries\Point;
use MStaack\LaravelPostgis\Geometries\MultiPoint;

use Illuminate\Support\Facades\Validator;

class CreateSegment {

    public static function create(array $input, Activity $activity)
    {
        extract(Validator::make($input, [
            'name' => 'required|string',
            'segmentStartTimestamp' => 'required',
            'segmentEndTimestamp' => 'required',
        ])->validate());

        $fit = new FitFile(Storage::path($activity->upload->file_path));
        $lats = $fit->data_mesgs['record']['position_lat'];
        $lons = $fit->data_mesgs['record']['position_long'];
        $dist = $fit->data_mesgs['record']['distance'];
        $alts = $fit->data_mesgs['record']['altitude'];

        $start_point = new Point($lats[$segmentStartTimestamp], $lons[$segmentStartTimestamp]);
        $end_point = new Point($lats[$segmentEndTimestamp], $lons[$segmentEndTimestamp]);
        $route = [];
        for($i = $segmentStartTimestamp; $i <= $segmentEndTimestamp; $i++) {
            if (isset($lats[$i], $lons[$i])) {
                $route[] = new Point($lats[$i], $lons[$i]);
            }
        }

        $segment = (new Segment([
            'name' => $name,
            'start_point' => $start_point,
            'end_point' => $end_point,
            'start_time' => $segmentStartTimestamp,
            'end_time' => $segmentEndTimestamp,
            'route' => new MultiPoint($route),
            'distance' => $dist[$segmentEndTimestamp] - $dist[$segmentStartTimestamp],
            'altitude_change' => $alts[$segmentEndTimestamp] - $alts[$segmentStartTimestamp],
        ]))->activity()->associate($activity);
        $segment->save();

        return $segment;
    }

}