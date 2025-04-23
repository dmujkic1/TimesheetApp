<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class Project extends Model
{

    use HasFactory;
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'project_name',
        'description',
        'start_date',
        'end_date',
        'status',
        /* 'team_id', */
        'client_id',
    ];

    public function team(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'projects_teams')->withTimestamps();
    }
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
