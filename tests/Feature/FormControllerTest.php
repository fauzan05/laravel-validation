<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FormControllerTest extends TestCase
{
    public function testLoginFailed(): void
    {
        $response = $this->post('/login', [
            'username' => '',
            'password' => ''
        ]);
        $response->assertStatus(400);
    }
    public function testLoginSuccess()
    {
        $response = $this->post('/login', [
            'username' => 'admin',
            'password' => 'rahasia'
        ]); 
        $response->assertStatus(200);
    }

}
