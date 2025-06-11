<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\Task;

class Comment extends Model
{
       protected $fillable = [
        'task_id',
        'user_id',
        'content',
    ];
public function user()
{
    return $this->belongsTo(User::class);
}
public function task()
{
    return $this->belongsTo(Task::class);
}
}
