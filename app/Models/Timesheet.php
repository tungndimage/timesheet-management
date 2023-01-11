<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timesheet extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'date',
        'difficulties',
        'todo',
    ];

    public function tasks() {
        return $this->hasMany(Task::class, 'timesheet_id', 'id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
