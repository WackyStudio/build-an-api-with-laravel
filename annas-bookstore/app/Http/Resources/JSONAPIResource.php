<?php

namespace App\Http\Resources;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;

class JSONAPIResource extends JsonResource
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
            'id' => (string)$this->id,
            'type' => $this->type(),
            'attributes' => $this->allowedAttributes(),
            'relationships' => $this->prepareRelationships(),
        ];
    }

    private function prepareRelationships(){
        $collection = collect(config("jsonapi.resources.{$this->type()}.relationships"))->flatMap(function($related){
            $relatedType = $related['type'];
            $relationship = $related['method'];
            return [
                $relatedType => [
                    'links' => [
                        'self'    => route(
                            "{$this->type()}.relationships.{$relatedType}",
                            ['id' => $this->id]
                        ),
                        'related' => route(
                            "{$this->type()}.{$relatedType}",
                            ['id' => $this->id]
                        ),
                    ],
                    'data' => $this->prepareRelationshipData($relatedType, $relationship),
                ],
            ];
        });

        return $collection->count() > 0 ? $collection : new MissingValue();
    }

    private function prepareRelationshipData($relatedType, $relationship){
        if($this->whenLoaded($relationship) instanceof MissingValue){
            return new MissingValue();
        }

        if($this->$relationship() instanceof BelongsTo){
            return new JSONAPIIdentifierResource($this->$relationship);
        }

        return JSONAPIIdentifierResource::collection($this->$relationship);
    }

    public function with($request)
    {
        $with = [];
        if ($this->included($request)->isNotEmpty()) {
            $with['included'] = $this->included($request);
        }

        return $with;
    }

    public function included($request)
    {
        return collect($this->relations())
            ->filter(function ($resource) {
                return $resource->collection !== null;
            })->flatMap->toArray($request);
    }

    private function relations()
    {
        return collect(config("jsonapi.resources.{$this->type()}.relationships"))->map(function($relation){
            $modelOrCollection = $this->whenLoaded($relation['method']);

            if($modelOrCollection instanceof Model){
                $modelOrCollection = collect([new JSONAPIResource($modelOrCollection)]);
            }

            return JSONAPIResource::collection($modelOrCollection);
        });
    }
}
