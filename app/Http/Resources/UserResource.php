<?php

namespace App\Http\Resources;

use App\Models\CallingKey;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            'user-first-name'   => $this->{"user-first-name"},
            'user-last-name'    => $this->{"user-last-name"},
            'phone-no'          => $this->{"phone-no"},
            'gender'            => $this->gender,
            'email'             => $this->email,
            'approval'          => $this->approval,
            'user-type'         => $this->{"user-type"},
            'call-key'          => new CallingKeyResource($this->callingKey),
            'university'        => new UniversityResource($this->university),
            'user-stage'        => new StageResource($this->stage),
            'image'             => url($this->image),
            'date-of-add'       => $this->{"date-of-add"},
            'date-of-edit'      => $this->{"date-of-edit"},
            'neighbourhoods'    => NeighbourResource::collection($this->university->cityUni->neighbours),
        ];
    }
}
