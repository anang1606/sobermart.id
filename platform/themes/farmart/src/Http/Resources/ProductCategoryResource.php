<?php

namespace Theme\Farmart\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use RvMedia;

class ProductCategoryResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            // 'url' => $this->url,
            // 'slug' => $this->slug,
            'category_type' => $this->category_type,
            'image' => RvMedia::getImageUrl($this->image, null, false, RvMedia::getDefaultImage()),
            'thumbnail' => RvMedia::getImageUrl($this->image, 'small', false, RvMedia::getDefaultImage()),
        ];
    }
}
