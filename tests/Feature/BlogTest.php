<?php

use App\Models\BlogPost;
use App\Models\DoctorProfile;

/*
 | The published/draft/scheduled rules matter more than anything else on the
 | blog: a draft leaking onto the public site would be a real problem for a
 | doctor, so each state gets its own test.
 */

beforeEach(function () {
    DoctorProfile::create([
        'name' => 'Dr. Test Example',
        'specialization' => 'Consultant Cardiologist',
    ]);
});

it('lists published articles newest first', function () {
    $older = BlogPost::factory()->create([
        'title' => 'The older article',
        'published_at' => now()->subMonth(),
    ]);

    $newer = BlogPost::factory()->create([
        'title' => 'The newer article',
        'published_at' => now()->subDay(),
    ]);

    $response = $this->get(route('blog.index'))->assertOk();

    expect(strpos($response->getContent(), $newer->title))
        ->toBeLessThan(strpos($response->getContent(), $older->title));
});

it('shows a published article', function () {
    $post = BlogPost::factory()->create([
        'title' => 'Five heart symptoms',
        'content' => '<p>Some helpful advice.</p>',
    ]);

    $this->get(route('blog.show', $post->slug))
        ->assertOk()
        ->assertSee('Five heart symptoms')
        ->assertSee('Some helpful advice.', escape: false);
});

it('hides a draft article from the list and returns 404 for its url', function () {
    $draft = BlogPost::factory()->draft()->create(['title' => 'Unfinished draft']);

    $this->get(route('blog.index'))
        ->assertOk()
        ->assertDontSee('Unfinished draft');

    $this->get(route('blog.show', $draft->slug))->assertNotFound();
});

it('hides an article scheduled for the future until its date arrives', function () {
    $scheduled = BlogPost::factory()->create([
        'title' => 'Scheduled for next week',
        'is_published' => true,
        'published_at' => now()->addWeek(),
    ]);

    $this->get(route('blog.show', $scheduled->slug))->assertNotFound();

    // Travel past the publish date and it becomes visible with no further action.
    $this->travelTo(now()->addWeeks(2));

    $this->get(route('blog.show', $scheduled->slug))->assertOk();
});

it('paginates the article list', function () {
    config()->set('site.blog_per_page', 2);

    BlogPost::factory()->count(5)->create();

    $this->get(route('blog.index'))
        ->assertOk()
        ->assertSee('page=2', escape: false);
});

it('falls back to the body when no excerpt was written', function () {
    $post = BlogPost::factory()->create([
        'excerpt' => null,
        'content' => '<p>The opening paragraph of the article.</p>',
    ]);

    expect($post->excerpt())->toContain('The opening paragraph');
});
