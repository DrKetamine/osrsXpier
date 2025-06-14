<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PathStep extends Model
{
    use HasFactory;

    protected $fillable = ['path_id', 'action_id', 'level_from', 'level_to', 'step_order'];

    public function path()
    {
        return $this->belongsTo(Path::class);
    }

    public function action()
    {
        return $this->belongsTo(Action::class);
    }
}
