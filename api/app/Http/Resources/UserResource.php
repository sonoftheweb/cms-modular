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
            'email' => $this->email,
            'active' => $this->active,
            'status' => ($this->active) ? 'Active' : 'Not Active'
        ];

        if (isset($this->attribute))
            $res['attribute'] = $this->attribute;

        if (isset($this->role)) {
            $res['role'] = $this->role;
            $res['role_name'] = ($this->role->role_name === 'account_manager') ? 'Manager' : 'Client';
        }

        if (isset($this->instance))
            $res['instance'] = $this->instance;

        return $res;
    }
}
