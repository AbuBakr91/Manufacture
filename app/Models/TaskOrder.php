<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PerformingTasks;
use App\Models\TechnicalCards;
use App\Models\Department;

class TaskOrder extends Model
{
    use HasFactory;

    protected $hidden = ['created_at', 'updated_at'];

    protected $fillable = ['in_work', 'user_count'];

    public function taskDeportment()
    {
        return $this->belongsTo(Department::class, 'dep_id');
    }

    public function taskCardName()
    {
        return $this->belongsTo(TechnicalCards::class, 'card_id');
    }

    public function userTask()
    {
        return $this->hasMany(PerformingTasks::class, 'task_id');
    }
}
