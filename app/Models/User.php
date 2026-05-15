<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'firstname', 'address', 'email', 'mobile', 'password', 'role', 'parent_user_id',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    public function isCollaborator(): bool
    {
        return $this->role === 'collaborator';
    }

    public function companies()
    {
        return $this->hasMany(Company::class);
    }

    /** Companies accessible via pivot table (for collaborators) */
    public function accessibleCompanies()
    {
        return $this->belongsToMany(Company::class, 'user_company_access');
    }

    /** Returns all companies the user can work with, regardless of role */
    public function getWorkableCompanies()
    {
        if ($this->isCollaborator()) {
            return $this->accessibleCompanies()->orderBy('name')->get();
        }
        return $this->companies()->orderBy('name')->get();
    }

    /** Collaborators created by this manager */
    public function collaborators()
    {
        return $this->hasMany(User::class, 'parent_user_id');
    }

    /** Parent manager (for collaborators) */
    public function parentUser()
    {
        return $this->belongsTo(User::class, 'parent_user_id');
    }

    public function getFullNameAttribute(): string
    {
        return $this->firstname . ' ' . $this->name;
    }
}
