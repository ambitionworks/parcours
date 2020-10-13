<?php

return [
    /**
     * Used for the mapping of activities and segments.
     */
    'osm_key' => env('OSM_KEY'),

    /**
     * Used to determine the local time an activity occurred when processing an
     * activity by asking Google for the timezone at the start lat/lng/time.
     */
    'google_tz_key' => env('GOOGLE_TZ_KEY'),
];