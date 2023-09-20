<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserSampleResource extends JsonResource
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
            'image'             => url($this->image),
            'date-of-add'       => $this->{"date-of-add"},
            'date-of-edit'      => $this->{"date-of-edit"}
        ];
    }
}
