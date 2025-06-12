<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    protected $fillable = [
        'name', 'image', 'level', 'xp', 'quantity',
        'buy', 'sell', 'margin', 'margin_percent', 'members_only',
    ];

    public function ingredients()
    {
        return $this->hasMany(Ingredient::class);
    }
}

// app/Models/Ingredient.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    protected $fillable = ['action_id', 'name', 'image', 'quantity'];

    public function action()
    {
        return $this->belongsTo(Action::class);
    }
}
