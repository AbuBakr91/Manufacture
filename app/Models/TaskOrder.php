<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PerformingTasks;

class TaskOrder extends Model
{
    use HasFactory;

    protected $hidden = ['created_at', 'updated_at'];

    protected $fillable = ['in_work', 'user_count'];

    public function userTask()
    {
        return $this->hasMany(PerformingTasks::class, 'task_id');
    }
}
