<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Team extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'team_name',
        'manager_id',
    ];

    
    public function employee(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'employees_teams')->withTimestamps();
    }
    public function manager():BelongsTo
    {
        return $this->belongsTo(Manager::class);
    }
    public function project(): BelongsToMany
    {
        return $this->belongsToMany(Project::class)->withTimestamps();
    }


    
}
