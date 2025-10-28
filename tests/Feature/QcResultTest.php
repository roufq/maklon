<?php

namespace Tests\Feature;

use App\Models\QcResult;
use App\Models\ProductionBatch;
use App\Models\QualityCheckpoint;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class QcResultTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $batch;
    protected $checkpoint;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user for authentication
        $this->user = User::factory()->create();
        $this->user->assignRole('Admin'); // Assign admin role for full access
        $this->actingAs($this->user);

        // Create a batch and checkpoint for QC results
        $this->batch = ProductionBatch::factory()->create();
        $this->checkpoint = QualityCheckpoint::factory()->create();
    }

    #[Test]
    public function it_can_list_qc_results()
    {
        QcResult::factory()->count(3)->create([
            'production_batch_id' => $this->batch->id,
            'quality_checkpoint_id' => $this->checkpoint->id,
            'by_user_id' => $this->user->id
        ]);

        $response = $this->get(route('quality.results.index'));

        $response->assertStatus(200);
        $response->assertViewHas('items');
        $response->assertViewHas('items', function ($items) {
            return $items->count() === 3;
        });
    }

    #[Test]
    public function it_can_create_a_qc_result()
    {
        $qcData = [
            'production_batch_id' => $this->batch->id,
            'quality_checkpoint_id' => $this->checkpoint->id,
            'status' => 'pass',
            'notes' => 'All checks passed successfully',
        ];

        $response = $this->post(route('quality.results.store'), $qcData);

        $response->assertRedirect(route('quality.results.show', QcResult::latest()->first()));
        $this->assertDatabaseHas('qc_results', array_merge($qcData, ['by_user_id' => $this->user->id]));
    }

    #[Test]
    public function it_can_show_a_qc_result()
    {
        $qcResult = QcResult::factory()->create([
            'production_batch_id' => $this->batch->id,
            'quality_checkpoint_id' => $this->checkpoint->id,
            'by_user_id' => $this->user->id
        ]);

        $response = $this->get(route('quality.results.show', $qcResult));

        $response->assertStatus(200);
        $response->assertViewHas('result', $qcResult);
    }

    #[Test]
    public function it_can_update_a_qc_result()
    {
        $qcResult = QcResult::factory()->create([
            'production_batch_id' => $this->batch->id,
            'quality_checkpoint_id' => $this->checkpoint->id,
            'by_user_id' => $this->user->id
        ]);

        $updatedData = [
            'production_batch_id' => $this->batch->id,
            'quality_checkpoint_id' => $this->checkpoint->id,
            'status' => 'fail',
            'notes' => 'Failed due to contamination',
        ];

        $response = $this->put(route('quality.results.update', $qcResult), $updatedData);

        $response->assertRedirect(route('quality.results.show', $qcResult));
        $this->assertDatabaseHas('qc_results', array_merge($updatedData, ['by_user_id' => $this->user->id]));
    }

    #[Test]
    public function it_can_delete_a_qc_result()
    {
        $qcResult = QcResult::factory()->create([
            'production_batch_id' => $this->batch->id,
            'quality_checkpoint_id' => $this->checkpoint->id,
            'by_user_id' => $this->user->id
        ]);

        $response = $this->delete(route('quality.results.destroy', $qcResult));

        $response->assertRedirect(route('quality.results.index'));
        $this->assertDatabaseMissing('qc_results', ['id' => $qcResult->id]);
    }

    #[Test]
    public function it_validates_required_fields_when_creating_qc_result()
    {
        $response = $this->post(route('quality.results.store'), []);

        $response->assertSessionHasErrors(['production_batch_id', 'quality_checkpoint_id', 'status']);
    }

    #[Test]
    public function it_validates_required_fields_when_updating_qc_result()
    {
        $qcResult = QcResult::factory()->create([
            'production_batch_id' => $this->batch->id,
            'quality_checkpoint_id' => $this->checkpoint->id,
            'by_user_id' => $this->user->id
        ]);

        $response = $this->put(route('quality.results.update', $qcResult), []);

        $response->assertSessionHasErrors(['production_batch_id', 'quality_checkpoint_id', 'status']);
    }

    #[Test]
    public function it_can_filter_qc_results_by_status()
    {
        QcResult::factory()->create([
            'status' => 'pass',
            'production_batch_id' => $this->batch->id,
            'quality_checkpoint_id' => $this->checkpoint->id,
            'by_user_id' => $this->user->id
        ]);

        QcResult::factory()->create([
            'status' => 'fail',
            'production_batch_id' => $this->batch->id,
            'quality_checkpoint_id' => $this->checkpoint->id,
            'by_user_id' => $this->user->id
        ]);

        $response = $this->get(route('quality.results.index', ['status' => 'pass']));

        $response->assertStatus(200);
        $response->assertViewHas('items', function ($items) {
            return $items->count() === 1 && $items->first()->status === 'pass';
        });
    }
}
