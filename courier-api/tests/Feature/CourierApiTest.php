<?php

namespace Tests\Feature;

use App\Models\Courier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourierApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_persists_valid_courier_and_rejects_invalid_level(): void
    {
        $payload = ['name' => 'Budi Agung', 'phone' => '0812345678', 'email' => 'budi@example.test', 'level' => 3];
        $this->postJson('/api/couriers', $payload)->assertCreated()->assertJsonPath('name', 'Budi Agung');
        $this->assertDatabaseHas('couriers', ['email' => 'budi@example.test', 'level' => 3]);
        $this->postJson('/api/couriers', ['name' => 'Bad', 'level' => 6])->assertUnprocessable()->assertJsonValidationErrors('level');
    }

    public function test_index_searches_all_name_terms_filters_levels_sorts_and_paginates(): void
    {
        Courier::create(['name' => 'Budiono Hadi Agung', 'level' => 2, 'registered_at' => '2024-01-01']);
        Courier::create(['name' => 'Agung Budi', 'level' => 3, 'registered_at' => '2025-01-01']);
        Courier::create(['name' => 'Budi Lain', 'level' => 1, 'registered_at' => '2026-01-01']);

        $this->getJson('/api/couriers?search=budi+agung&level=2,3&per_page=1')
            ->assertOk()->assertJsonPath('total', 2)->assertJsonPath('data.0.name', 'Agung Budi')
            ->assertJsonPath('per_page', 1);
        $this->getJson('/api/couriers?sort=registered_at&direction=desc')
            ->assertOk()->assertJsonPath('data.0.name', 'Budi Lain');
    }

    public function test_show_returns_all_fields_and_update_persists_changes(): void
    {
        $courier = Courier::create(['name' => 'Budi', 'level' => 1]);
        $this->getJson("/api/couriers/{$courier->id}")->assertOk()->assertJsonPath('level', 1);
        $this->patchJson("/api/couriers/{$courier->id}", ['name' => 'Budi Agung', 'level' => 5])
            ->assertOk()->assertJsonPath('name', 'Budi Agung');
        $this->assertDatabaseHas('couriers', ['id' => $courier->id, 'name' => 'Budi Agung', 'level' => 5]);
    }

    public function test_destroy_removes_courier(): void
    {
        $courier = Courier::create(['name' => 'Budi', 'level' => 1]);
        $this->deleteJson("/api/couriers/{$courier->id}")->assertNoContent();
        $this->assertDatabaseMissing('couriers', ['id' => $courier->id]);
    }
}
