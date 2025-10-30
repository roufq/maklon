<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuthAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function guest_is_redirected_to_login_for_protected_routes()
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    #[Test]
    public function user_without_permission_gets_forbidden()
    {
        $user = User::factory()->create();
        $user->assignRole('CS'); // CS does not have inventory.view by default

        $this->actingAs($user);

        $response = $this->get(route('inventory.index'));
        $response->assertStatus(403);
    }
}
