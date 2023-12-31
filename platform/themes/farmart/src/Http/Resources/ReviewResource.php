<?php

namespace Theme\Farmart\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use RvMedia;

class ReviewResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'user_name' => $this->user->name,
            'user_avatar' => $this->user->avatar_url,
            'created_at_tz' => $this->created_at->translatedFormat('Y-m-d\TH:i:sP'),
            'created_at' => $this->created_at->diffForHumans(),
            'comment' => $this->comment,
            'id' => $this->id,
            'star' => $this->star,
            'parent' => collect($this->parent)->map(function ($parent){
                return [
                    'vendor' => $parent->vendor->name,
                    'comment' => $parent->comment,
                    'created_at' => $parent->created_at->diffForHumans()
                ];
            }),
            'images' => collect($this->images)->map(function ($image) {
                return [
                    'thumbnail' => RvMedia::getImageUrl($image, 'thumb'),
                    'full_url' => RvMedia::getImageUrl($image),
                ];
            }),
            'ordered_at' => $this->order_created_at ? __(
                '✅ Purchased :time',
                ['time' => Carbon::createFromDate($this->order_created_at)->diffForHumans()]
            ) : null,
        ];
    }
}
