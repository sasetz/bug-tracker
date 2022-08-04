<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Test if the guest dashboard has all required information. WIP
     *
     * @return void
     */
    public function test_guest_dashboard(): void
    {
        $response = $this->get('/');

        $response->assertOk();
    }

    /**
     * Simple user registration test.
     * 
     * @return void
     */
    public function test_registration(): void
    {
        $user_data = [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => fake()->password(8),
        ];
        $user_data['password_confirmation'] = $user_data['password'];
        
        $request = $this->post('/register', $user_data);
        
        $request->assertOk();
        $this->assertGuest();
        $this->assertDatabaseHas('users', [
            'name' => $user_data['name'],
            'email' => $user_data['email'],
        ]);
    }

    /**
     * Simple login test.
     * 
     * @return void
     */
    public function test_login(): void
    {
        $user = User::factory()->create();
        
        $request = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);
        
        $request->assertRedirect('/dashboard');
    }

    // finish the test after creating an SPA
    
//    /**
//     * Simple logout test.
//     * 
//     * @return void
//     */
//    public function test_logout(): void
//    {
//        Sanctum::actingAs(User::factory()->create(), ['*']);
//        $response = $this->post('/logout');
//        
//        $response->assertOk();
//        $this->assertGuest();
//    }
}
