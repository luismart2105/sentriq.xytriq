<?php

namespace Tests;

use PDO;

abstract class DatabaseTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (! in_array('sqlite', PDO::getAvailableDrivers(), true)) {
            $this->markTestSkipped('PDO SQLite no está disponible en este servidor.');
        }

        $this->artisan('migrate:fresh', ['--force' => true]);
    }
}
