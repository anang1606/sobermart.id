<?php

namespace Botble\Marketplace\Models;

use Botble\Base\Casts\SafeContent;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Botble\Base\Supports\Avatar;
use Botble\Ecommerce\Models\Customer;
use Botble\Ecommerce\Models\Discount;
use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Traits\LocationTrait;
use Exception;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use RvMedia;

class Store extends BaseModel
{
    use LocationTrait;

    protected $table = 'mp_stores';
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'country',
        'state',
        'city',
		'kecamatan',
		'kelurahan',
		'zip_code',
        'customer_id',
		'idktp',
        'ktp',
		'logo',
		'covers',
        'description',
        'content',
        'status',
        'company',
        'origin_shipment',
        'branch_shipment',
    ];

    protected $casts = [
        'status' => BaseStatusEnum::class,
        'name' => SafeContent::class,
        'description' => SafeContent::class,
        'content' => SafeContent::class,
        'address' => SafeContent::class,
    ];

    protected static function boot()
    {
        parent::boot();

        self::deleting(function (Store $store) {
            Product::where('store_id', $store->id)->delete();
            Discount::where('store_id', $store->id)->delete();
            Order::where('store_id', $store->id)->update(['store_id' => null]);
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class)->withDefault();
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class)->where('is_finished', 1);
    }

    public function getLogoUrlAttribute(): ?string
    {
        if ($this->logo) {
            return RvMedia::getImageUrl($this->logo, 'thumb');
        }

        try {
            return (new Avatar())->create($this->name)->toBase64();
        } catch (Exception) {
            return RvMedia::getDefaultImage();
        }
    }

    public function reviews(): HasMany
    {
        return $this
            ->hasMany(Product::class)
            ->join('ec_reviews', 'ec_products.id', '=', 'ec_reviews.product_id');
    }
}
