<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimeSheet extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'start_time',
        'end_time',
        'break_start',
        'break_end',
        'notes',
        'project_id',
        'user_id',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    /* public function employee(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'employees_teams')->withTimestamps();
    }
    public function manager():BelongsTo
    {
        return $this->belongsTo(Manager::class);
    }
    public function project(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'projects_teams')->withTimestamps();
    } */
}
