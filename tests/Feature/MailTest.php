<?php

namespace Tests\Feature;

use App\Models\Attachment;
use App\Models\Email;
use App\Models\User;
use App\Mail\DefaultEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MailTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function test_if_send_endpoint_doesnt_accepts_base64_attachment()
    {
        Mail::fake();

        $user = User::factory()->create();

        // non base64 attachment
        $postData = $this->prepareEmail();
        $postData['attachment'] = [
            [
                'name'  => 'some-name.jpg',
                'value' => 'not/base64',
            ],
        ];

        $this->actingAs($user)
            ->postJson('/api/send', [$postData])
            ->assertStatus(422);

        Mail::assertNothingSent();
    }

    public function test_if_email_is_not_dispatched_when_validation_error_occurs()
    {
        Mail::fake();

        $user = User::factory()->create();

        // missing email
        $postData = $this->prepareEmail();
        unset($postData['email']);
        $this->actingAs($user)
            ->postJson('/api/send', [$postData])
            ->assertStatus(422);

        Mail::assertNothingSent();
    }

    public function test_if_cant_send_emails_with_invalid_data()
    {
        $user = User::factory()->create();

        // missing email
        $postData = $this->prepareEmail();
        unset($postData['email']);
        $this->actingAs($user)
            ->postJson('/api/send', [$postData])
            ->assertStatus(422);

        // missing subject
        $postData = $this->prepareEmail();
        unset($postData['subject']);
        $this->actingAs($user)
            ->postJson('/api/send', [$postData])
            ->assertStatus(422);

        // missing body
        $postData = $this->prepareEmail();
        unset($postData['body']);
        $this->actingAs($user)
            ->postJson('/api/send', [$postData])
            ->assertStatus(422);
    }

    public function test_if_email_gets_dispatched_correctly()
    {
        Mail::fake();

        $user = User::factory()->create();

        $postData = $this->prepareEmail();

        $this->actingAs($user)
            ->postJson('/api/send', [$postData])
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'email',
                        'subject',
                        'body',
                        'user_id',
                        'updated_at',
                        'created_at',
                    ],
                ],
            ]);

        Mail::assertSent(
            DefaultEmail::class,
            function ($mail) use ($postData) {
                return $mail->hasTo($postData['email']) &&
                    $mail->body === $postData['body'] &&
                    $mail->subject === $postData['subject'] &&
                    count($mail->diskAttachments) === 1;
            }
        );
    }

    public function test_if_emails_array_gets_dispatched_correctly()
    {
        Mail::fake();

        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/api/send', [
                $this->prepareEmail(),
                $this->prepareEmail(),
            ]);

        $response->assertStatus(200);

        Mail::assertSent(DefaultEmail::class, 2);
    }

    public function test_if_dispatched_email_is_persisted_in_the_db()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson('/api/send', [
                $this->prepareEmail(),
                $this->prepareEmail(),
            ])
            ->assertStatus(200);

        $this->assertDatabaseCount('emails', 2);
    }

    public function test_can_get_list_of_emails()
    {
        $user  = User::factory()->create();
        $email = Email::factory()->create([
            'user_id' => $user->id,
        ]);
        $attachments = Attachment::factory()->count(2)->create([
            'email_id' => $email->id,
        ]);

        $expectedResponse = $email->toArray();
        $expectedResponse['attachments'] = $attachments->toArray();

        $this->actingAs($user)
            ->get('/api/list')
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    $expectedResponse,
                ],
            ]);
    }

    private function prepareEmail(bool $withAttachments = true): array
    {
        $fakeEmail   = $this->faker->email;
        $fakeSubject = $this->faker->sentence(2);
        $fakeBody    = $this->faker->sentence(10);

        $fakeAttachments = [];
        if ($withAttachments) {
            Storage::fake('local');
            $fakeAttachments = [
                [
                    'name' => $this->faker->word(),
                    'value' => file_get_contents(__DIR__ . '/../Assets/base64image-sample.txt'),
                ]
            ];
        }

        return [
            'email' => $fakeEmail,
            'subject' => $fakeSubject,
            'body' => $fakeBody,
            'attachments' => $fakeAttachments,
        ];
    }
}
