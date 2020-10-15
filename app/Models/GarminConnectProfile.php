<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use dawguk\GarminConnect;

class GarminConnectProfile extends Model
{

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Garmin Connect API.
     *
     * @var GarminConnect
     */
    protected GarminConnect $api;

    /**
     * The exception message if a connection to the API failed.
     *
     * @var string
     */
    protected string $apiExceptionMessage = '';

    /**
     * Undocumented function
     *
     * @return GarminConnect|class
     */
    public function api()
    {
        try {
            $this->api = new GarminConnect(['username' => $this->email, 'password' => unserialize(Crypt::decryptString($this->password))]);
        } catch (\Exception $e) {
            $this->apiExceptionMessage = $e->getMessage();
            Log::warning($e->getMessage(), [$this->email]);
            // Return an anonymous class so chained calls made against the
            // return of this method will not completely blow up.
            return new class {
                public function __call($method, $arguments) {
                    return null;
                }
            };
        }

        return $this->api;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getApiException(): string
    {
        return $this->apiExceptionMessage;
    }
}
