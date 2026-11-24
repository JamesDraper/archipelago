<?php
declare(strict_types=1);

namespace Tests\Unit\ExceptionFactory;

use Archipelago_Internal_ExceptionFactory_BadValueExceptionFactory;
use Archipelago_BadValueException;

use Tests\Unit\TestCase;

use PHPUnit\Framework\Attributes\Test;

final class BadValueExceptionFactoryTest extends TestCase
{
    private readonly Archipelago_Internal_ExceptionFactory_BadValueExceptionFactory $factory;

    #[Test]
    public function it_makes_an_exception(): void
    {
        $exception = $this->factory->makeException('Some message.');

        $this->assertInstanceOf(Archipelago_BadValueException::class, $exception);
        $this->assertSame('Some message.', $exception->getMessage());
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = new Archipelago_Internal_ExceptionFactory_BadValueExceptionFactory();
    }
}
