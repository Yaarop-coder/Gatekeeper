<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use App\Models\Tenant;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

        protected function setUp(): void
    {
        parent::setUp();
    
        // Create the default tenant with a slug to satisfy the DB
        $tenant = Tenant::first() ?? Tenant::create(['name' => 'Default', 'slug' => 'default']);
    
        // Act as this tenant globally if your logic requires it
        // app()->instance(Tenant::class, $tenant); 
    }
    protected function actingAsTenant($tenant)
    {
        return $this->withSession(['tenant_id' => $tenant->id]);
    }
}