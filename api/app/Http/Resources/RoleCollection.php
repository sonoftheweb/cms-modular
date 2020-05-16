<?php

namespace App\Http\Resources;

use App\Helpers\SmartTableHeadersDefinitions;
use Illuminate\Http\Resources\Json\ResourceCollection;

class RoleCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $collection = $this->collection->transform(function ($roles) {
            return new RoleResource($roles);
        });

        return [
            'data' => $collection,
            'headers' => SmartTableHeadersDefinitions::roles()
        ];
    }
}
