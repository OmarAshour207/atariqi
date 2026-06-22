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

            $responseBody = $response->json();


            if($response->status() == 400) {

                if(isset($responseBody['resultCode']) && $responseBody['resultCode'] == 'bad_request') {
                    throw new \Exception($responseBody['resultMsg'] ?? __('Failed to register driver to Wasl'));
                }

                $waslCode = $responseBody['resultCode'] ?? '';
                throw new \Exception($this->getWaslCode($waslCode));
            }

            if ( isset($responseBody['result']['eligibility']) && $responseBody['result']['eligibility'] === 'INVALID' ) {
                $reasons = $responseBody['result']['rejectionReasons'] ?? [];
                $messages = collect($reasons)->map(function ($code) {
                    return $this->getWaslCode($code);
                })->values()->toArray();
                throw new \Exception(implode(' | ', $messages));
            }

            // return $response->json();

        } catch (\Exception $e) {
            Log::channel('wasl')->error('Error registering driver to Wasl', ['driver_id' => $driver->id, 'error' => $e->getMessage()]);
            throw new \Exception($e->getMessage());
        }

    }

    public function translateWaslCode(?string $code): string
    {
        if (!$code) {
            return __('Unknown ministry reason');
        }

        $mapping = [
            'DRIVER_VEHICLE_DUPLICATE' => __('Driver or vehicle already registered'),
            'DRIVER_NOT_ALLOWED' => __('Foreign nationalities are not allowed per TGA rules'),
            'DRIVER_NOT_FOUND' => __('Driver information is not correct. Kindly revise input data before re-attempting registration'),
            'VEHICLE_NOT_FOUND' => __('Vehicle information is not correct. Kindly revise input data before re-attempting registration'),
            'VEHICLE_NOT_OWNED_BY_FINANCIER' => __('Vehicle ownership is not associated with the driver nor approved by SAMA financer (Check BR05 in business rules)'),
            'DRIVER_NOT_AUTHORIZED_TO_DRIVE_VEHICLE' => __('Driver does not own the vehicle and there is no legal association between the driver and vehicle (Check BR05)'),
            'NO_VALID_OPERATION_CARD' => __('No valid operating card found (Check BR05)'),
            'CONTACT_WASL_SUPPORT' => __('System internal error or missing data. Kindly contact Wasl Support'),
            'NO_OPERATIONAL_CARD_FOUND' => __('No valid operation card found (check BR08 in driver and vehicle registration service business rules section)',),

            // Residency & Identity
            'ALIEN_LEGAL_STATUS_NOT_VALID' => __('Alien residency is not valid'),
            'DRIVER_IDENTITY_EXPIRED' => __('Driver identity is expired'),

            // Age Rules
            'MAX_AGE_NOT_SATISFIED' => __('Driver age is greater than 65'),
            'MIN_AGE_NOT_SATISFIED' => __('Driver age is less than 18'),

            // Driver Status
            'DRIVER_IS_BANNED' => __('Driver is banned from practicing dispatching activities per TGA order'),
            'DRIVER_LICENSE_EXPIRED' => __('Driver license is expired'),
            'DRIVER_LICENSE_NOT_ALLOWED' => __('Driver license type is not allowed'),

            // Criminal Record
            'DRIVER_FAILED_CRIMINAL_RECORD_CHECK' => __('Driver is ineligible due to criminal record check result'),
            'DRIVER_REJECTED_CRIMINAL_RECORD_CHECK' => __('Driver declined criminal record check on Absher portal'),
            'CRIMINAL_RECORD_CHECK_PERIOD_EXPIRED' => __('Driver did not respond to criminal record check within 10 days'),
            'DRIVER_REJECTED_MANY_CRIMINAL_RECORD_CHECK' => __('Driver rejected or ignored criminal record check 3 or more times. Contact Wasl support'),

            // Vehicle Status
            'VEHICLE_INSURANCE_EXPIRED' => __('Vehicle insurance has expired'),
            'VEHICLE_LICENSE_EXPIRED' => __('Vehicle license has expired'),
            'VEHICLE_NOT_INSURED' => __('Vehicle does not have valid insurance'),
            'OLD_VEHICLE_MODEL' => __('Vehicle model is older than 5 years'),
            'VEHICLE_PLATE_TYPE_NOT_ALLOWED' => __('Vehicle license type/category is not allowed'),
            'VEHICLE_ELIGIBILITY_EXPIRED' => __('Vehicle eligibility has expired'),

            // Inspection
            'PERIODIC_INSPECTION_POLICY_EXPIRED' => __('Vehicle periodic inspection has expired'),
            'NO_PERIODIC_INSPECTION_POLICY_EXPIRY_DATE' => __('Vehicle does not have a periodic inspection expiry date'),

            // Operation Card
            'OPERATION_CARD_EXPIRED' => __('Vehicle operation card has expired'),
            'NO_VALID_OPERATION_CARD_FOUND' => __('Vehicle does not have a valid operation card'),

            // Eligibility
            'DRIVER_ELIGIBILITY_EXPIRED' => __('Driver eligibility status has expired'),
            'DRIVER_VEHICLE_INELIGIBLE' => __('Driver does not have an eligible vehicle. Fix vehicle rejection reasons and re-register'),

        ];

        return $mapping[$code] ?? $code;
    }

    /**
     * @deprecated Use translateWaslCode()
     */
    private function getWaslCode($code)
    {
        return $this->translateWaslCode($code);
    }

    public function buildEligibilityRequestBody(User $driver): ?array
    {
        $identityNumber = trim((string) ($driver->driverInfo?->identity_number ?? ''));

        if ($identityNumber === '') {
            return null;
        }

        return $this->formatEligibilityPayload($identityNumber);
    }

    public function formatEligibilityPayload(string $driverId): array
    {
        return [
            'driverIds' => [
                ['id' => (string) $driverId],
            ],
        ];
    }

    public function checkDriverEligibility(?string $identityNumber, ?array $body = null): ?array
    {
        $identityNumber = trim((string) ($identityNumber ?? ''));

        if ($identityNumber === '') {
            Log::channel('wasl')->warning('Skipping WASL eligibility check: identity number is missing');

            return null;
        }
        Log::channel('wasl')->info('Checking driver eligibility with Wasl', [
            'identity_number' => $identityNumber,
            'body' => $body,
        ]);

        if (!$this->config['enabled']) {
            return null;
        }

        $payload = $body ?? $this->formatEligibilityPayload($identityNumber);

        try {
            $response = Http::withHeaders([
                'client-id' => $this->config['client_key'],
                'app-id' => $this->config['app_id'],
                'app-key' => $this->config['app_key'],
            ])
                ->contentType('application/json')
                ->post($this->config['api_url'] . '/api/dispatching/v2/drivers/eligibility', $payload);

            Log::channel('wasl')->info('Received response from Wasl for driver eligibility check', [
                'identity_number' => $identityNumber,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            if (!$response->successful()) {
                $responseBody = $response->json();
                throw new \Exception($responseBody['resultMsg'] ?? $response->body());
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::channel('wasl')->error('Error checking driver eligibility with Wasl', [
                'identity_number' => $identityNumber,
                'error' => $e->getMessage(),
            ]);
            throw new \Exception('Wasl API Error: ' . $e->getMessage());
        }
    }

    public function parseEligibilityResponse(?array $response, ?string $identityNumber = null): array
    {
        if (!$response) {
            return [
                'is_valid' => null,
                'reasons' => [],
                'message' => __('Unknown'),
                'display_status' => __('Unknown'),
                'driver_eligibility' => null,
                'vehicle_eligibility' => null,
            ];
        }

        if (isset($response['responses']) && is_array($response['responses'])) {
            $item = collect($response['responses'])->first(function ($row) use ($identityNumber) {
                if (!$identityNumber) {
                    return false;
                }

                return ($row['identityNumber'] ?? null) == $identityNumber
                    || ($row['id'] ?? null) == $identityNumber;
            }) ?? ($response['responses'][0] ?? null);

            return $this->parseEligibilityItem(is_array($item) ? $item : []);
        }

        if (isset($response['result']) && is_array($response['result'])) {
            return $this->parseEligibilityItem($response['result']);
        }

        return $this->parseEligibilityItem($response);
    }

    public function parseEligibilityItem(array $item): array
    {
        $driverEligibility = strtoupper((string) ($item['driverEligibility'] ?? $item['eligibility'] ?? ''));
        $vehicleEligibility = null;
        $vehicleInvalid = false;

        if (!empty($item['vehicles']) && is_array($item['vehicles'])) {
            $firstVehicle = $item['vehicles'][0] ?? [];
            $vehicleEligibility = strtoupper((string) ($firstVehicle['vehicleEligibility'] ?? ''));
            $vehicleInvalid = $vehicleEligibility === 'INVALID';

            foreach ($item['vehicles'] as $vehicle) {
                if (strtoupper((string) ($vehicle['vehicleEligibility'] ?? '')) === 'INVALID') {
                    $vehicleInvalid = true;
                    break;
                }
            }
        }

        $reasonCodes = $item['rejectionReasons'] ?? [];
        if (is_string($reasonCodes)) {
            $reasonCodes = [$reasonCodes];
        }

        $reasons = collect($reasonCodes)
            ->filter()
            ->map(fn ($code) => $this->translateWaslCode((string) $code))
            ->unique()
            ->values()
            ->all();

        $isValid = !in_array($driverEligibility, ['INVALID', 'INELIGIBLE'], true)
            && !$vehicleInvalid
            && empty($reasons);

        if (in_array($driverEligibility, ['VALID', 'ELIGIBLE'], true) && !$vehicleInvalid) {
            $isValid = true;
        }

        if ($driverEligibility === 'INVALID' || $vehicleInvalid || !empty($reasons)) {
            $isValid = false;
        }

        return [
            'is_valid' => $isValid,
            'reasons' => $reasons,
            'message' => !empty($reasons) ? implode(' | ', $reasons) : ($isValid ? __('Valid') : __('Not Valid')),
            'display_status' => $isValid ? __('Valid') : __('Not Valid'),
            'driver_eligibility' => $driverEligibility ?: null,
            'vehicle_eligibility' => $vehicleEligibility,
        ];
    }

    public function applyDailyEligibilityToDriver(User $driver): void
    {
        if (!filled($driver->driverInfo?->identity_number)) {
            return;
        }

        $driver->loadMissing(['driverInfo', 'callingKey']);
        $body = $this->buildEligibilityRequestBody($driver);

        if ($body === null) {
            return;
        }

        $response = $this->checkDriverEligibility($driver->driverInfo->identity_number, $body);
        $parsed = $this->parseEligibilityResponse($response, $driver->driverInfo->identity_number);

        if ($parsed['is_valid'] === null) {
            return;
        }

        if ($parsed['is_valid'] === false) {
            $driver->update([
                'approval' => 4,
                'reject-reason' => $parsed['message'],
            ]);

            return;
        }

        if ((int) $driver->approval === 4) {
            $driver->update([
                'approval' => 0,
                'reject-reason' => null,
            ]);
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
