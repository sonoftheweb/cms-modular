<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $res = [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email
        ];

        if (isset($this->attribute))
            $res['attribute'] = $this->attribute;

        if (isset($this->role))
            $res['role'] = $this->role;

        if (isset($this->instance))
            $res['instance'] = $this->instance;

        return $res;
    }
}
