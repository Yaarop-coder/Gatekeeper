<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/** * We'll use the traditional protected properties for compatibility
 * with the Trait and the rest of the setup for now.
 */
class User extends Authenticatable
{
    use BelongsToTenant, HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'tenant_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function tenant()
    {
        // A user belongs to one tenant
        return $this->belongsTo(Tenant::class);
    }

    public function getInitialsAttribute()
    {
        // Split the name by spaces (e.g., "Steve Jobs" -> ["Steve", "Jobs"])
        $words = explode(' ', $this->name);

        if (count($words) >= 2) {
            // Take first letter of first word and first letter of second word
            return strtoupper(substr($words[0], 0, 1).substr($words[1], 0, 1));
        }

        // If it's just one name, take the first two letters
        return strtoupper(substr($this->name, 0, 2));
    }
}
