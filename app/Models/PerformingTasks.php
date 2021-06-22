<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\WorkWaiting;
use App\Models\WorkPaused;

class PerformingTasks extends Model
{
    use HasFactory;

    public function taskPaused()
    {
        return $this->hasMany(WorkPaused::class, 'work_id');
    }

    public function taskWaiting()
    {
        return $this->hasMany(WorkWaiting::class, 'work_id');
    }

}
