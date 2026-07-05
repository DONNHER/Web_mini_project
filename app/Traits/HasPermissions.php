<?php

namespace App\Traits;

use App\Models\Role;
use App\Models\Permission;

trait HasPermissions
{
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function hasPermission($permission)
    {
        if ($this->isAdmin()) {
            return true;
        }

        return (bool) $this->role->permissions->where('name', $permission)->count();
    }

    public function hasModuleAccess($module)
    {
        if ($this->isAdmin()) {
            return true;
        }

        return (bool) $this->role->permissions->where('module', $module)->count();
    }

    public function isAdmin()
    {
        return $this->role && $this->role->name === 'admin';
    }
    
    public function isBorrower()
    {
        return $this->role && $this->role->name === 'borrower';
    }

    public function isGuest()
    {
        return $this->role && $this->role->name === 'guest';
    }
}
