<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Helpers\SmartTableHeadersDefinitions;

class UserCollection extends BaseCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $collection = $this->collection->transform(function ($users) {
            return new UserResource($users);
        });

        return [
            'data' => $collection,
            'headers' => SmartTableHeadersDefinitions::users(),
            'meta' => $this->meta
        ];
    }
}
