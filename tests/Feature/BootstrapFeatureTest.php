<?php

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;

/**
 * Test minimal Feature pour vérifier que PHPUnit fonctionne
 */
class BootstrapFeatureTest extends TestCase
{
    /**
     * Test basique Feature pour vérifier que PHPUnit est opérationnel
     */
    public function testPhpUnitFeatureWorks(): void
    {
        $this->assertTrue(true);
    }
}

