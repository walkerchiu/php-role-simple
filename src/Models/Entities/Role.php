<?php

namespace WalkerChiu\RoleSimple\Models\Entities;

use WalkerChiu\Core\Models\Entities\Entity;

class Role extends Entity
{
    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = array())
    {
        $this->table = config('wk-core.table.role-simple.roles');
        $this->fillable = array_merge($this->fillable, [
            'host_type', 'host_id',
            'serial', 'identifier',
            'name', 'description'
        ]);

        parent::__construct($attributes);
    }

    /**
     * Get the owning host model.
     */
    public function host()
    {
        return $this->morphTo();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(config('wk-core.class.user'),
                                    config('wk-core.table.role-simple.users_roles'),
                                    'role_id',
                                    'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function permissions()
    {
        return $this->belongsToMany(config('wk-core.class.role-simple.permission'),
                                    config('wk-core.table.role-simple.roles_permissions'),
                                    'role_id',
                                    'permission_id');
    }

    /**
     * Checks if the role has a permission.
     *
     * @param String|Array $value
     * @return Boolean
     */
    public function hasPermission($value)
    {
        if (is_string($value)) {
            return $this->permissions->where('identifier', $value)
                                     ->count() > 0 ? true : false;
        } elseif (is_array($value)) {
            return $this->permissions->whereIn('identifier', $value)
                                     ->count() > 0 ? true : false;
        }

        return false;
    }

    /**
     * Checks if the role has permissions in the same time.
     *
     * @param Array $value
     * @return Boolean
     */
    public function hasPermissions(Array $permissions)
    {
        $result = false;

        foreach ($permissions as $permission) {
            $result = $this->permissions->where('identifier', $value)
                                        ->count() > 0 ? true : false;
            if (!$result) {
                break;
            }
        }

        return $result;
    }

    /**
     * Alias to eloquent many-to-many relation's attach() method.
     *
     * @param mixed $role
     * @return None
     */
    public function attachPermission($permission)
    {
        if (is_object($permission)) {
            $permission = $permission->getKey();
        }

        if (is_array($permission)) {
            $permission = $permission['id'];
        }

        $this->perms()->attach($permission);
    }

    /**
     * Alias to eloquent many-to-many relation's detach() method.
     *
     * @param mixed $role
     * @return None
     */
    public function detachPermission($permission)
    {
        if (is_object($permission)) {
            $permission = $permission->getKey();
        }

        if (is_array($permission)) {
            $permission = $permission['id'];
        }

        $this->perms()->detach($permission);
    }

    /**
     * Attach multiple permissions to current role.
     *
     * @param mixed $roles
     * @return None
     */
    public function attachPermissions($permissions)
    {
        foreach ($permissions as $permission) {
            $this->attachPermission($permission);
        }
    }

    /**
     * Detach multiple permissions from current role
     *
     * @param mixed $roles
     * @return None
     */
    public function detachPermissions($permissions = null)
    {
        if (!$permissions) {
            $permissions = $this->permissions()->get();
        }

        foreach ($permissions as $permission) {
            $this->detachPermission($permission);
        }
    }
}
