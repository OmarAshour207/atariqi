<?php

namespace App\Services;

use App\Http\Resources\Driver\Wasl\RegisterResource;
use App\Http\Resources\Driver\Wasl\UpdateCurrentLocationResource;
use App\Http\Resources\Driver\Wasl\UpdateTripDataResource;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WaslService
{
    protected $config;

    public function __construct()
    {
        $this->config = config('wasl');
    }


    public function registerDriver(User $driver)
    {
        Log::channel('wasl')->info('Registering driver to Wasl', ['driver_id' => $driver->id]);

        if (!$this->config['enabled']) {
            return null;
        }

        $driverData = new RegisterResource($driver);
        $driverData = $driverData->resolve();

        Log::channel('wasl')->info('Prepared driver data for Wasl', ['driver_id' => $driver->id, 'data' => $driverData]);

        try {
            $response = Http::withHeaders([
                'client-id' => $this->config['client_key'],
                'app-id'     => $this->config['app_id'],
                'app-key'    => $this->config['app_key']
            ])
            ->contentType('application/json')
            ->post($this->config['api_url'] . '/api/dispatching/v2/drivers', $driverData);

            Log::channel('wasl')->info('Received response from Wasl for driver registration', ['driver_id' => $driver->id, 'status' => $response->status(), 'body' => $response->body()]);

            if ($response->successful()) {
                return $response->json();
            }

            $responseBody = $response->json();

            throw new \Exception($responseBody['resultMsg'] ?? $response->body());

        } catch (\Exception $e) {
            Log::channel('wasl')->error('Error registering driver to Wasl', ['driver_id' => $driver->id, 'error' => $e->getMessage()]);
            throw new \Exception($e->getMessage());
        }

    }

    // Store the trip once finished
    public function storeTrip($trip)
    {
        Log::channel('wasl')->info('Storing trip to Wasl', ['trip_id' => $trip->id]);

        $tripData = new UpdateTripDataResource($trip);
        $tripData = $tripData->resolve();

        Log::channel('wasl')->info('Prepared trip data for Wasl', ['trip_id' => $trip->id, 'data' => $tripData]);

        try {
            $response = Http::withHeaders([
                'client-id' => $this->config['client_key'],
                'app-id'     => $this->config['app_id'],
                'app-key'    => $this->config['app_key']
            ])
            ->contentType('application/json')
            ->post($this->config['api_url'] . '/api/dispatching/v2/trips', $tripData);

            Log::channel('wasl')->info('Received response from Wasl', ['trip_id' => $trip->id, 'status' => $response->status(), 'body' => $response->body()]);

            if ($response->successful()) {
                return $response->json();
            }

            $responseBody = $response->json();

            throw new \Exception($responseBody['resultMsg'] ?? $response->body());

        } catch (\Exception $e) {
            Log::channel('wasl')->error('Error storing trip to Wasl', ['trip_id' => $trip->id, 'error' => $e->getMessage()]);
            throw new \Exception('Wasl API Error: ' . $e->getMessage());
        }
    }

    // Update Trip location and once the driver ready to start on the app
    public function updateTripLocation($trip)
    {
        Log::channel('wasl')->info('Updating trip location to Wasl', ['trip_id' => $trip->id]);

        $tripData = new UpdateCurrentLocationResource($trip);
        $tripData = $tripData->resolve();

        Log::channel('wasl')->info('Prepared location data for Wasl', ['trip_id' => $trip->id, 'data' => $tripData]);

        try {
            $response = Http::withHeaders([
                'client-id' => $this->config['client_key'],
                'app-id'     => $this->config['app_id'],
                'app-key'    => $this->config['app_key']
            ])
            ->contentType('application/json')
            ->post($this->config['api_url'] . '/api/dispatching/v2/locations', $tripData);

            Log::channel('wasl')->info('Received response from Wasl for location update', ['trip_id' => $trip->id, 'status' => $response->status(), 'body' => $response->body()]);

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception($response->body());

        } catch (\Exception $e) {
            Log::channel('wasl')->error('Error updating trip location to Wasl', ['trip_id' => $trip->id, 'error' => $e->getMessage()]);
            throw new \Exception('Wasl API Error: ' . $e->getMessage());
        }
    }

}
