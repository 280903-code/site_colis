<?php

namespace Tests\Feature;

use Tests\TestCase;

class PublicInformationPagesTest extends TestCase
{
    public function test_about_page_is_available_from_the_public_navigation(): void
    {
        $this->get(route('about'))
            ->assertOk()
            ->assertSee('AB-Flash aide chacun')
            ->assertSee('Toutes les agences')
            ->assertSee(route('contact'));
    }

    public function test_contact_page_shows_the_official_contact_information(): void
    {
        $this->get(route('contact'))
            ->assertOk()
            ->assertSee('elaminebenabdou68@gmail.com')
            ->assertSee('+221 77 440 95 66')
            ->assertSee('https://wa.me/221774409566');
    }
}
