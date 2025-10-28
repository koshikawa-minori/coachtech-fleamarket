<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ItemShowTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    // 必要な情報が表示される
    public function test_item_required_information_display()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
