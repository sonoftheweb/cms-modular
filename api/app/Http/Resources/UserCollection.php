<?php

namespace App\Http\Resources;

use App\Helpers\SmartTableHeadersDefinitions;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserCollection extends ResourceCollection
{
    /**
     * @var $meta
     */
    private $meta;

    /**
     * UserCollection constructor.
     *
     * @param $resource
     */
    public function __construct($resource)
    {
        $this->meta = [
            'total' => $resource->total(),
            'count' => $resource->count(),
            'per_page' => $resource->perPage(),
            'current_page' => $resource->currentPage(),
            'total_pages' => $resource->lastPage()
        ];

        $resource = $resource->getCollection();

        parent::__construct($resource);
    }

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
