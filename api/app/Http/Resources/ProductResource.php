<?php

namespace App\Http\Resources;

use App\Helpers\Utils;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request):array
    {
        return [
            'stripe_product_identifier' => $this->stripe_product_identifier,
            'name' => $this->name,
            'statement_descriptor' => $this->statement_descriptor,
            'unit_label' => $this->unit_label,
            'active' => $this->active,
            'metadata' => (Utils::is_valid_json($this->metadata)) ? json_decode($this->metadata) : $this->metadata,
            'created' => $this->created,
            'plans' => PlanResource::collection($this->plans)
        ];
    }
}
