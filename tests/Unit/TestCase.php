<?php

declare(strict_types=1);

namespace Tests\Unit;

use function spl_autoload_register;
use function str_replace;
use function dirname;
use function is_file;

abstract class TestCase extends \Tests\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->autoload();
    }

    private function autoload(): void
    {
        spl_autoload_register(static function (string $classPath): void {
            $filePath = dirname(__DIR__, 2)
                . '/archipelago/src/'
                . str_replace('_', '/', $classPath)
                . '.php';

            if (is_file($filePath)) {
                require $filePath;
            }
        });
    }
}
