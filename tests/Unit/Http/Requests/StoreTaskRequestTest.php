<?php

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\StoreTaskRequest;
use App\TaskPriority;
use App\TaskStatus;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StoreTaskRequestTest extends TestCase
{
    private function validate(array $overrides = []): \Illuminate\Contracts\Validation\Validator
    {
        $data = array_merge([
            'title' => 'Buy milk',
            'description' => null,
            'due_date' => null,
            'status' => TaskStatus::Pending->value,
            'priority' => TaskPriority::Low->value,
        ], $overrides);

        return Validator::make($data, (new StoreTaskRequest)->rules());
    }

    public function test_valid_payload_passes(): void
    {
        $this->assertTrue($this->validate()->passes());
    }

    public function test_missing_title_fails(): void
    {
        $validator = $this->validate(['title' => '']);

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('title'));
    }

    public function test_title_longer_than_255_characters_fails(): void
    {
        $validator = $this->validate(['title' => str_repeat('a', 256)]);

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('title'));
    }

    public function test_null_description_and_due_date_pass(): void
    {
        $this->assertTrue($this->validate(['description' => null, 'due_date' => null])->passes());
    }

    public function test_due_date_that_is_not_a_date_fails(): void
    {
        $validator = $this->validate(['due_date' => 'not-a-date']);

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('due_date'));
    }

    public function test_missing_status_fails(): void
    {
        $validator = $this->validate(['status' => '']);

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('status'));
    }

    public function test_status_outside_the_enum_fails(): void
    {
        $validator = $this->validate(['status' => 'archived']);

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('status'));
    }

    public function test_missing_priority_fails(): void
    {
        $validator = $this->validate(['priority' => '']);

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('priority'));
    }

    public function test_priority_outside_the_enum_fails(): void
    {
        $validator = $this->validate(['priority' => 'urgent']);

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('priority'));
    }
}
