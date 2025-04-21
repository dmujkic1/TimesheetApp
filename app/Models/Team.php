<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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

    
    public function employee(): HasMany
    {
        return $this->hasMany(Employee::class);
    }
    public function manager():BelongsTo
    {
        return $this->belongsTo(Manager::class);
    }
    public function project():HasOne
    {
        return $this->hasOne(Project::class);
    }


    
}
