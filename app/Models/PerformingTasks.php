<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\WorkWaiting;
use App\Models\WorkPaused;
use App\Models\User;
use App\Models\TechnicalCards;

class PerformingTasks extends Model
{
    use HasFactory;

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function userName()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function taskPaused()
    {
        return $this->hasMany(WorkPaused::class, 'work_id');
    }

    public function taskWaiting()
    {
        return $this->hasMany(WorkWaiting::class, 'work_id');
    }

}
