<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RegisterTenantController extends Controller
{
    public function __invoke(Request $request)
    {
        // 1. Validation (Always protect your house)
        $data = $request->validate([
            'company_name' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        // 2. The Transaction (The All-or-Nothing block)
        return DB::transaction(function () use ($data) {

            // Create the Tenant
            $tenant = Tenant::create([
                'name' => $data['company_name'],
                'slug' => Str::slug($data['company_name']),
            ]);

            // Create the User and link them to the new Tenant
            $user = User::create([
                'tenant_id' => $tenant->id,
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
                'role' => 'owner', // They created the tenant, so they own it!
            ]);

            return response()->json([
                'message' => 'Tenant and User created successfully!',
                'tenant' => $tenant,
                'user' => $user,
            ], 201);
        });
    }
}
