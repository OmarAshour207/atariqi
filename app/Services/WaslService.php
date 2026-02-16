<?php

namespace App\Services;

use App\Http\Resources\Driver\Wasl\RegisterResource;
use App\Http\Resources\Driver\Wasl\UpdateCurrentLocationResource;
use App\Http\Resources\Driver\Wasl\UpdateTripDataResource;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class WaslService
{
    protected $config;

    public function __construct()
    {
        $this->config = config('wasl');
    }


    public function registerDriver(User $driver)
    {
        if (!$this->config['enabled']) {
            return null;
        }

        $driverData = new RegisterResource($driver);
        $driverData = $driverData->resolve();

        try {
            $response = Http::withHeaders([
                'client-id' => $this->config['client_key'],
                'app-id'     => $this->config['app_id'],
                'app-key'    => $this->config['app_key']
            ])
            ->contentType('application/json')
            ->post($this->config['api_url'] . '/api/dispatching/v2/drivers', $driverData);

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception($response->body());

        } catch (\Exception $e) {
            throw new \Exception('Wasl API Error: ' . $e->getMessage());
        }

    }

    // Store the trip once finished
    public function storeTrip($trip)
    {
        $tripData = new UpdateTripDataResource($trip);
        $tripData = $tripData->resolve();

        try {
            $response = Http::withHeaders([
                'client-id' => $this->config['client_key'],
                'app-id'     => $this->config['app_id'],
                'app-key'    => $this->config['app_key']
            ])
            ->contentType('application/json')
            ->post($this->config['api_url'] . '/api/dispatching/v2/trips', $tripData);

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception($response->body());

        } catch (\Exception $e) {
            throw new \Exception('Wasl API Error: ' . $e->getMessage());
        }
    }

    // Update Trip location and once the driver ready to start on the app
    public function updateTripLocation($trip)
    {
        $tripData = new UpdateCurrentLocationResource($trip);
        $tripData = $tripData->resolve();

        try {
            $response = Http::withHeaders([
                'client-id' => $this->config['client_key'],
                'app-id'     => $this->config['app_id'],
                'app-key'    => $this->config['app_key']
            ])
            ->contentType('application/json')
            ->post($this->config['api_url'] . '/api/dispatching/v2/locations', $tripData);

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception($response->body());

        } catch (\Exception $e) {
            throw new \Exception('Wasl API Error: ' . $e->getMessage());
        }
    }

}
