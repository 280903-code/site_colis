<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_renders_the_konexa_slideshow(): void
    {
        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('Vos colis voyagent mieux avec KONEXA.')
            ->assertSee('data-home-carousel', false)
            ->assertSee('fond.jpeg')
            ->assertSee('hero-flight.jpg')
            ->assertSee('hero-airport.jpg')
            ->assertSee('home-carousel.js');
    }
}
