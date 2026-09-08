<?php

namespace App\Http\Resources;

use App\Models\WeekRideBooking;
use Illuminate\Http\Resources\Json\JsonResource;

class WeekRideBookingResource extends JsonResource
{
    /** @var array<string, int> */
    private static array $weeklyDaysCountCache = [];

    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'group_id'      => $this->{"group-id"},
            'date_of_ser'   => $this->{"date-of-ser"},
            'road_way'      => $this->{"road-way"},
            'time_go'       => $this->{"time-go"},
            'time_back'     => $this->{"time-back"},
            'lat'           => $this->{"lat"},
            'lng'           => $this->{"lng"},
            'action'        => $this->{"action"},
            'weekly_days_count' => $this->resolveWeeklyDaysCount(),
            'neighborhood'  => new NeighbourResource($this->neighborhood),
            'passenger'     => new UserSampleResource($this->passenger),
            'university'    => new UniversityResource($this->university),
            'service_id'    => new ServiceResource($this->service),
        ];
    }

    private function resolveWeeklyDaysCount(): int
    {
        if (isset($this->resource->weekly_days_count)) {
            return max(1, (int) $this->resource->weekly_days_count);
        }

        $groupId = (string) ($this->{"group-id"} ?? '');

        if ($groupId === '') {
            return 1;
        }

        if (! array_key_exists($groupId, self::$weeklyDaysCountCache)) {
            $count = (int) WeekRideBooking::query()
                ->where('group-id', $groupId)
                ->selectRaw('COUNT(DISTINCT `date-of-ser`) as aggregate')
                ->value('aggregate');

            self::$weeklyDaysCountCache[$groupId] = max(1, $count);
        }

        return self::$weeklyDaysCountCache[$groupId];
    }
}
