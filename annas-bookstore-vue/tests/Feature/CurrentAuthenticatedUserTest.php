<?php


namespace Tests\Feature;


use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Passport\Passport;
use Tests\TestCase;

class CurrentAuthenticatedUserTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     * @watch
     */
    public function it_returns_the_current_authenticated_user_as_a_resource_object()
    {
        $this->withoutExceptionHandling();
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->getJson("/api/v1/users/current", [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ]) ->assertStatus(200)
            ->assertJson([
                "data" => [
                    "id" => $user->id,
                    "type" => "users",
                    "attributes" => [
                        'name' => $user->name,
                        'email' => $user->email,
                        'created_at' => $user->created_at->toJSON(),
                        'updated_at' => $user->updated_at->toJSON(),
                    ]
                ]
            ]);

    }

}
