<?php

namespace Tests\Feature;

use App\Models\Idea;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ShowIdeasTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_of_ideas_shows_on_main_page()
    {
        $ideaOne = Idea::factory()->create([
            'title' => 'My first idea',
            'description' => 'Description of my first idea',
        ]);

        $ideaTwo = Idea::factory()->create([
            'title' => 'My second idea',
            'description' => 'Description of my second idea',
        ]);

        $response = $this->get(route('idea.index'));

        $response->assertSuccessful();
        $response->assertSee($ideaOne->title);
        $response->assertSee($ideaOne->description);
        $response->assertSee($ideaTwo->title);
        $response->assertSee($ideaTwo->description);

    }

    public function test_single_idea_shows_on_show_page()
    {
        $idea = Idea::factory()->create([
            'title' => 'My first idea',
            'description' => 'Description of my first idea',
        ]);

        $response = $this->get(route('idea.show', $idea));

        $response->assertSuccessful();
        $response->assertSee($idea->title);
        $response->assertSee($idea->description);

    }

    public function test_ideas_pagination_works()
    {
        Idea::factory(Idea::PAGINATION_COUNT + 1)->create();

        $ideaOnFirstPage = Idea::find(1);
        $ideaOnFirstPage->title = 'My first Idea';
        $ideaOnFirstPage->save();

        $ideaOnSecondPage = Idea::find(11);
        $ideaOnSecondPage->title = 'My Eleventh Idea';
        $ideaOnSecondPage->save();

        $response = $this->get('/');

        $response->assertSee($ideaOnFirstPage->title);
        $response->assertDontSee($ideaOnSecondPage->title);

        $response = $this->get('/?page=2');

        $response->assertSee($ideaOnSecondPage->title);
        $response->assertDontSee($ideaOnFirstPage->title);
    }

    public function test_same_idea_different_slugs()
    {
        $ideaOne = Idea::factory()->create([
            'title' => 'My first idea',
            'description' => 'This is all about the best idea ever',
        ]);

        $ideaTwo = Idea::factory()->create([
            'title' => 'My first idea',
            'description' => 'This is all about the best idea ever, super original',
        ]);

        $response = $this->get(route('idea.show', $ideaOne));

        $response->assertSuccessful();
        $this->assertTrue(request()->path() === 'ideas/my-first-idea');

        $response = $this->get(route('idea.show', $ideaTwo));

        $response->assertSuccessful();
        $this->assertTrue(request()->path() === 'ideas/my-first-idea-2');
    }

}
