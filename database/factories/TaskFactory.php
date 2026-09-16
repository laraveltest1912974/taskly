<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use App\TaskPriority;
use App\TaskStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    /**
     * Realistic English and Serbian todo items, so seeded data reads like a
     * real task list instead of Faker's lorem-ipsum sentences.
     *
     * @var array<int, array{title: string, description: string|null}>
     */
    private const TASK_POOL = [
        ['title' => 'Buy groceries', 'description' => 'Milk, eggs, bread, and vegetables for the week.'],
        ['title' => 'Kupiti namirnice', 'description' => 'Mleko, jaja, hleb i povrće za nedelju dana.'],
        ['title' => 'Call the dentist', 'description' => 'Schedule a check-up appointment.'],
        ['title' => 'Pozvati zubara', 'description' => 'Zakazati pregled.'],
        ['title' => 'Finish the quarterly report', 'description' => 'Include Q3 sales numbers and next steps.'],
        ['title' => 'Završiti kvartalni izveštaj', 'description' => 'Uključiti brojke prodaje za Q3 i sledeće korake.'],
        ['title' => 'Clean the apartment', 'description' => 'Vacuum, dust, and take out the trash.'],
        ['title' => 'Očistiti stan', 'description' => 'Usisati, obrisati prašinu i izneti smeće.'],
        ['title' => 'Pay the electricity bill', 'description' => null],
        ['title' => 'Platiti račun za struju', 'description' => null],
        ['title' => 'Renew passport', 'description' => 'Passport expires next month.'],
        ['title' => 'Obnoviti pasoš', 'description' => 'Pasoš ističe sledećeg meseca.'],
        ['title' => 'Book flight tickets', 'description' => 'For the trip to Belgrade in October.'],
        ['title' => 'Rezervisati avionske karte', 'description' => 'Za put u Beograd u oktobru.'],
        ['title' => 'Water the plants', 'description' => null],
        ['title' => 'Zaliti biljke', 'description' => null],
        ['title' => 'Prepare presentation for Monday', 'description' => 'Cover the new project roadmap.'],
        ['title' => 'Pripremiti prezentaciju za ponedeljak', 'description' => 'Obraditi mapu puta novog projekta.'],
        ['title' => 'Fix the leaking faucet', 'description' => null],
        ['title' => 'Popraviti slavinu koja curi', 'description' => null],
        ['title' => 'Read 20 pages of the new book', 'description' => null],
        ['title' => 'Pročitati 20 strana nove knjige', 'description' => null],
        ['title' => 'Go for a 30-minute run', 'description' => null],
        ['title' => 'Otrčati 30 minuta', 'description' => null],
        ['title' => 'Update the resume', 'description' => 'Add the latest work experience.'],
        ['title' => 'Ažurirati CV', 'description' => 'Dodati najnovije radno iskustvo.'],
        ['title' => 'Schedule car service', 'description' => 'Oil change and tire rotation.'],
        ['title' => 'Zakazati servis automobila', 'description' => 'Zamena ulja i rotacija guma.'],
        ['title' => 'Buy a birthday gift for mom', 'description' => null],
        ['title' => 'Kupiti rođendanski poklon za mamu', 'description' => null],
        ['title' => 'Backup important files', 'description' => 'Photos and work documents.'],
        ['title' => 'Napraviti bekap važnih fajlova', 'description' => 'Fotografije i radni dokumenti.'],
        ['title' => 'Submit the tax return', 'description' => null],
        ['title' => 'Predati poresku prijavu', 'description' => null],
        ['title' => 'Organize the garage', 'description' => null],
        ['title' => 'Srediti garažu', 'description' => null],
        ['title' => 'Learn 10 new Spanish words', 'description' => null],
        ['title' => 'Naučiti 10 novih španskih reči', 'description' => null],
        ['title' => 'Plan weekend trip to the mountains', 'description' => 'Check weather forecast first.'],
        ['title' => 'Isplanirati vikend izlet u planine', 'description' => 'Prvo proveriti vremensku prognozu.'],
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $task = fake()->randomElement(self::TASK_POOL);

        return [
            'user_id' => User::factory(),
            'title' => $task['title'],
            'description' => $task['description'],
            'due_date' => fake()->optional()->dateTimeBetween('now', '+1 month'),
            'status' => fake()->randomElement(TaskStatus::cases()),
            'priority' => fake()->randomElement(TaskPriority::cases()),
        ];
    }

    /**
     * Indicate that the task is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TaskStatus::Completed,
        ]);
    }
}
