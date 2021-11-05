<?php

namespace App\Domain\Server\Tests\Feature;

use Tests\TestCase;

class ServerFeatureTest extends TestCase
{
    public function testCanSeeHealth()
    {
        $this->get('/')
            ->assertSee('health');
    }
}
