<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MovieControllerTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/movie/search');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Searching movie "Harry Potter"');
    }
}
