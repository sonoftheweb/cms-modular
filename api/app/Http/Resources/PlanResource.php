<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PlanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'pid' => $this->stripe_plan_identifier,
            'product_id' => $this->product,
            'currency' => $this->currency,
            'nickname' => $this->nickname,
            'interval' => $this->interval,
            'interval_count' => $this->interval_count,
            'tiers' => json_decode($this->tiers, true)
        ];
    }
}
