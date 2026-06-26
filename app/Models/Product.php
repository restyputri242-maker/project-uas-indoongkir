<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'description', 'price', 'stock', 'weight', 'image_path'])]
class Product extends Model
{
    use HasFactory;

    /**
     * Get the formatted price in IDR.
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    /**
     * Get the formatted weight.
     */
    public function getFormattedWeightAttribute(): string
    {
        if ($this->weight >= 1000) {
            return ($this->weight / 1000) . ' kg';
        }
        return $this->weight . ' gr';
    }
}