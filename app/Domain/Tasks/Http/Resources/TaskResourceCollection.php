<?php

namespace App\Domain\Tasks\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class TaskResourceCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return ['tasks' => TaskResource::collection($this->collection)];
    }
}
