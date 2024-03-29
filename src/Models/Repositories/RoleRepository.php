<?php

namespace WalkerChiu\RoleSimple\Models\Repositories;

use Illuminate\Support\Facades\App;
use WalkerChiu\Core\Models\Forms\FormHasHostTrait;
use WalkerChiu\Core\Models\Repositories\Repository;
use WalkerChiu\Core\Models\Repositories\RepositoryHasHostTrait;
use WalkerChiu\Core\Models\Services\PackagingFactory;

class RoleRepository extends Repository
{
    use FormHasHostTrait;
    use RepositoryHasHostTrait;

    protected $instance;



    /**
     * Create a new repository instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->instance = App::make(config('wk-core.class.role-simple.role'));
    }

    /**
     * @param Array  $data
     * @param Bool   $is_enabled
     * @param Bool   $auto_packing
     * @return Array|Collection|Eloquent
     */
    public function list(array $data, $is_enabled = null, $auto_packing = false)
    {
        $instance = $this->instance;
        if ($is_enabled === true)      $instance = $instance->ofEnabled();
        elseif ($is_enabled === false) $instance = $instance->ofDisabled();

        $data = array_map('trim', $data);
        $repository = $instance->when($data, function ($query, $data) {
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
                                ->orderBy('updated_at', 'DESC');

        if ($auto_packing) {
            $factory = new PackagingFactory(config('wk-role-simple.output_format'), config('wk-role-simple.pagination.pageName'), config('wk-role-simple.pagination.perPage'));
            return $factory->output($repository);
        }

        return $repository;
    }

    /**
     * @param Role  $instance
     * @return Role
     */
    public function show($instance)
    {
    }

    /**
     * @param String  $code
     * @return Array
     */
    public function getRoleSupported(): array
    {
        $records = $this->instance->get();

        $data = [];
        foreach ($records as $record) {
            array_push($data, ['id'   => $record->id,
                               'name' => $record->name]);
        }

        return $data;
    }
}
