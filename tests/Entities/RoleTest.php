<?php

namespace WalkerChiu\RoleSimple;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use WalkerChiu\RoleSimple\Models\Entities\Role;

class RoleTest extends \Orchestra\Testbench\TestCase
{
    use RefreshDatabase;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__ .'/../migrations');
        $this->withFactories(__DIR__ .'/../../src/database/factories');
    }

    /**
     * To load your package service provider, override the getPackageProviders.
     *
     * @param \Illuminate\Foundation\Application  $app
     * @return Array
     */
    protected function getPackageProviders($app)
    {
        return [\WalkerChiu\Core\CoreServiceProvider::class,
                \WalkerChiu\RoleSimple\RoleSimpleServiceProvider::class];
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
    }

    /**
     * A basic functional test on Role.
     *
     * For WalkerChiu\RoleSimple\Models\Entities\Role
     * 
     * @return void
     */
    public function testRole()
    {
        // Config
        Config::set('wk-core.onoff.core-lang_core', 0);
        Config::set('wk-role-simple.onoff.core-lang_core', 0);
        Config::set('wk-core.lang_log', 1);
        Config::set('wk-role-simple.lang_log', 1);
        Config::set('wk-core.soft_delete', 1);
        Config::set('wk-role-simple.soft_delete', 1);

        // Give
        $db_record_1 = factory(Role::class)->create();
        $db_record_2 = factory(Role::class)->create();
        $db_record_3 = factory(Role::class)->create(['is_enabled' => 1]);

        // Get records after creation
            // When
            $records = Role::all();
            // Then
            $this->assertCount(3, $records);

        // Delete someone
            // When
            $db_record_2->delete();
            $records = Role::all();
            // Then
            $this->assertCount(2, $records);

        // Resotre someone
            // When
            Role::withTrashed()
                ->find($db_record_2->id)
                ->restore();
            $record_2 = Role::find($db_record_2->id);
            $records = Role::all();
            // Then
            $this->assertNotNull($record_2);
            $this->assertCount(3, $records);

        // Scope query on enabled records
            // When
            $records = Role::ofEnabled()
                           ->get();
            // Then
            $this->assertCount(1, $records);

        // Scope query on disabled records
            // When
            $records = Role::ofDisabled()
                           ->get();
            // Then
            $this->assertCount(2, $records);
    }
}
