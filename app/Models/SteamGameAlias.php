<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SteamGameAlias extends Model
{
    protected $primaryKey = 'alias';

    public $incrementing = false;

    protected $fillable = ['appid', 'alias'];

    public function game()
    {
        return $this->belongsTo(SteamGame::class, 'appid', 'appid');
    }
}
