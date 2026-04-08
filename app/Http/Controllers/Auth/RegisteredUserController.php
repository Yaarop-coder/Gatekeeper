<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        // Fetch all tenants so the user can pick one
        $tenants = Tenant::all();

        return view('auth.register', compact('tenants'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Password::defaults()],
            'tenant_id' => ['nullable', 'exists:tenants,id'], // Validate the selection
        ]);

        $tenantId = $request->tenant_id;

        // Logic: If they didn't pick an ID, create a new Tenant
        if (! $tenantId) {
            $tenantName = $request->name."'s Team";
            $tenant = Tenant::create([
                'name' => $tenantName,
                'slug' => Str::slug($tenantName).'-'.Str::random(5),
            ]);
            $tenantId = $tenant->id;
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'tenant_id' => $tenantId, // Now they share the same ID!
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect(route('projects.index'));
    }
}
