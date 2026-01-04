<?php

namespace App\Http\Resources\Api\V1\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WardCommissionerDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'commissioner_id' => $this->commissioner_id,
            'date_of_birth' => $this->date_of_birth,
            'gender' => $this->gender,
            'blood_group' => $this->blood_group,
            'education' => $this->education,
            'profession' => $this->profession,
            'previous_experience' => $this->previous_experience,
            'achievements' => $this->achievements,
            'social_activities' => $this->social_activities,
            'emergency_contact' => $this->emergency_contact,
            'permanent_address_en' => $this->permanent_address_en,
            'permanent_address_bn' => $this->permanent_address_bn,
            'present_address_en' => $this->present_address_en,
            'present_address_bn' => $this->present_address_bn,
            'facebook_url' => $this->facebook_url,
            'twitter_url' => $this->twitter_url,
            'office_address_en' => $this->office_address_en,
            'office_address_bn' => $this->office_address_bn,
            'office_phone_en' => $this->office_phone_en,
            'office_phone_bn' => $this->office_phone_bn,
            'office_hours' => $this->office_hours,
            'public_meeting_schedule' => $this->public_meeting_schedule,
            'biography_en' => $this->biography_en,
            'biography_bn' => $this->biography_bn,
        ];
    }
}