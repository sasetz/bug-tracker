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

        $response->assertStatus(200);
    }

    /**
     * Simple user registration test.
     * 
     * @return void
     */
    public function test_registration(): void
    {
        $request = $this->post('/register', [
            'name' => 'Jane Doe',
            'email' => 'testing@gmail.com',
            'password' => 'VeryComplicatedPassword#2213',
            'password_confirmation' => 'VeryComplicatedPassword#2213',
        ]);
        
        $request->assertOk();
        $this->assertGuest();
        $this->assertDatabaseHas('users', [
            'name' => 'Jane Doe',
            'email' => 'testing@gmail.com',
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
