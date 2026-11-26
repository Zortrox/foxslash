<?php

namespace App\Models;

use Database\Factories\SteamGameFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class SteamGame extends Model
{
    /** @use HasFactory<SteamGameFactory> */
    use HasFactory, Searchable;

    public $primaryKey = 'appid';

    public $fillable = ['appid', 'name'];

    public function formattedName(): Attribute
    {
        return Attribute::make(
            get: fn () => str_replace('™', '', $this->name),
        )->shouldCache();
    }

    /**
     * Get the value used to index the model.
     *
     * @return mixed
     */
    public function getScoutKey()
    {
        return $this->appid;
    }

    public function getScoutKeyName()
    {
        return 'appid';
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        return array_merge($this->toArray(), [
            'id' => (string) $this->appid, // Typesense doesn't allow changing primary key
            'formatted_name' => $this->formatted_name,
            'formatted_name_length' => strlen($this->formatted_name),
            'created_at' => $this->created_at->timestamp,
        ]);
    }

    public function aliases()
    {
        return $this->hasMany(SteamGameAlias::class, 'appid', 'appid');
    }

    public function getAliasListAttribute()
    {
        return $this->aliases->pluck('alias')->toArray();
    }
}
