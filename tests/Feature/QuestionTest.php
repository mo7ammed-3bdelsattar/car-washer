<?php

namespace Tests\Feature;

use App\Models\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuestionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test storing a question with name and questions fields.
     */
    public function test_can_submit_question_with_name_and_questions(): void
    {
        $payload = [
            'name' => 'Ahmed Ali',
            'questions' => 'What are your working hours?',
        ];

        $response = $this->postJson('/api/v1/questions', $payload);

        $response->assertCreated()
            ->assertJson([
                'success' => true,
                'status' => 201,
                'message' => 'Question submitted successfully',
                'data' => [
                    'name' => 'Ahmed Ali',
                    'questions' => 'What are your working hours?',
                ],
            ]);

        $this->assertDatabaseHas('questions', [
            'name' => 'Ahmed Ali',
            'questions' => 'What are your working hours?',
        ]);
    }

    /**
     * Test storing a question with quiztions alias.
     */
    public function test_can_submit_question_with_quiztions_alias(): void
    {
        $payload = [
            'name' => 'Sara Omar',
            'quiztions' => 'Do you provide mobile car washing in Maadi?',
        ];

        $response = $this->postJson('/api/v1/questions', $payload);

        $response->assertCreated()
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => 'Sara Omar',
                    'questions' => 'Do you provide mobile car washing in Maadi?',
                ],
            ]);

        $this->assertDatabaseHas('questions', [
            'name' => 'Sara Omar',
            'questions' => 'Do you provide mobile car washing in Maadi?',
        ]);
    }

    /**
     * Test storing a question with area_quiztions input name.
     */
    public function test_can_submit_question_with_area_quiztions_input(): void
    {
        $payload = [
            'name' => 'Khaled Hassan',
            'area_quiztions' => 'Can I book a wash for tomorrow morning?',
        ];

        $response = $this->postJson('/api/v1/questions', $payload);

        $response->assertCreated()
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => 'Khaled Hassan',
                    'questions' => 'Can I book a wash for tomorrow morning?',
                ],
            ]);

        $this->assertDatabaseHas('questions', [
            'name' => 'Khaled Hassan',
            'questions' => 'Can I book a wash for tomorrow morning?',
        ]);
    }

    /**
     * Test validation failure when required fields are missing.
     */
    public function test_validation_fails_when_fields_are_missing(): void
    {
        $response = $this->postJson('/api/v1/questions', []);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'status' => 422,
            ])
            ->assertJsonValidationErrors(['name', 'questions']);
    }

    /**
     * Test getting questions publicly without auth.
     */
    public function test_can_get_questions_publicly(): void
    {
        Question::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/questions');

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'status' => 200,
            ])
            ->assertJsonCount(3, 'data');
    }

    /**
     * Test getting paginated questions.
     */
    public function test_can_get_paginated_questions(): void
    {
        Question::factory()->count(10)->create();

        $response = $this->getJson('/api/v1/questions?page=1&per_page=5');

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'status',
                'message',
                'data',
                'meta' => [
                    'current_page',
                    'per_page',
                    'total',
                ],
            ]);
    }
}
