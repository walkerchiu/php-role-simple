<?php

namespace WalkerChiu\RoleSimple\Models\Repositories;

use Illuminate\Support\Facades\App;
use WalkerChiu\Core\Models\Forms\FormTrait;
use WalkerChiu\Core\Models\Repositories\Repository;
use WalkerChiu\Core\Models\Repositories\RepositoryTrait;

class RoleRepository extends Repository
{
    use FormTrait;
    use RepositoryTrait;

    protected $entity;

    public function __construct()
    {
        $this->entity = App::make(config('wk-core.class.role-simple.role'));
    }

    /**
     * @param Array   $data
     * @param Int     $page
     * @param Int     $nums per page
     * @param Boolean $is_enabled
     * @return Collection
     */
    public function list(Array $data, $page = null, $nums = null, $is_enabled = null)
    {
        $this->assertForPagination($page, $nums);

        $entity = $this->entity;
        if ($is_enabled === true)      $entity = $entity->ofEnabled();
        elseif ($is_enabled === false) $entity = $entity->ofDisabled();

        $data = array_map('trim', $data);
        $records = $entity->when($data, function ($query, $data) {
                                return $query->unless(empty($data['id']), function ($query) use ($data) {
                                            return $query->where('id', $data['id']);
                                        })
                                        ->unless(empty($data['serial']), function ($query) use ($data) {
                                            return $query->where('serial', $data['serial']);
                                        })
                                        ->unless(empty($data['identifier']), function ($query) use ($data) {
                                            return $query->where('identifier', $data['identifier']);
                                        })
                                        ->unless(empty($data['name']), function ($query) use ($data) {
                                            return $query->where('name', $data['name']);
                                        })
                                        ->unless(empty($data['description']), function ($query) use ($data) {
                                            return $query->where('description', $data['description']);
                                        });
                            })
                            ->orderBy('updated_at', 'DESC')
                            ->get()
                            ->when(is_integer($page) && is_integer($nums), function ($query) use ($page, $nums) {
                                return $query->forPage($page, $nums);
                            });

        return $records;
    }

    /**
     * @param Role $entity
     * @return Role
     */
    public function show($entity)
    {
    }

    /**
     * @return Array
     */
    public function getRoleSupported()
    {
        $records = $this->entity->get();

        $data = [];
        foreach ($records as $record) {
            array_push($data, ['id'   => $record->id,
                               'name' => $record->name]);
        }

        return $data;
    }
}
