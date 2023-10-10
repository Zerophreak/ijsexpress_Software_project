<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'ean',
        'chip_id',
        'logo_url'
    ];

    /**
     * The freezers associated with this product, including the stock information.
     *
     * @return BelongsToMany
     */
    public function freezers(): BelongsToMany
    {
        return $this->belongsToMany(Freezer::class)
            ->withPivot('current_stock', 'max_stock')
            ->using(FreezerProductConnection::class);
    }

    /**
     * Retrieve the pivot connection of this product with a specific freezer.
     *
     * @param  Freezer  $freezer
     * @return FreezerProductConnection|null
     */
    public function freezer(Freezer $freezer): ?FreezerProductConnection
    {
        if (!$this->freezers->contains($freezer)) {
            return null;
        }

        return $this->freezers->find($freezer->id)->pivot;
    }

    /**
     * Retrieve all the mutations of this product.
     *
     * @return HasMany
     */
    public function mutations(): HasMany
    {
        return $this->hasMany(Mutation::class);
    }

    /**
     * Get all of the rfid tags for the Product
     *
     * @return HasMany
     */
    public function rfidTags(): HasMany
    {
        return $this->hasMany(RfidTag::class);
    }
}
