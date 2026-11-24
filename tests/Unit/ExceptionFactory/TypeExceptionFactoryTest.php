<?php
declare(strict_types=1);

namespace Tests\Unit\ExceptionFactory;

use Archipelago_Internal_ExceptionFactory_TypeExceptionFactory;
use Archipelago_TypeException;

use Tests\Unit\TestCase;


use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

use stdClass;

/**
 * @phpstan-type ArgumentScenario array{
 *     pathOrObject: string|object,
 *     method: string,
 *     position: int,
 *     expected: string,
 *     nullable: bool,
 *     value: mixed,
 *     message: string
 * }
 * @phpstan-type ReturnScenario array{
 *     pathOrObject: string|object,
 *     method: string,
 *     expected: string,
 *     nullable: bool,
 *     value: mixed,
 *     message: string
 * }
 */
final class TypeExceptionFactoryTest extends TestCase
{
    private readonly Archipelago_Internal_ExceptionFactory_TypeExceptionFactory $factory;

    #[Test]
    #[DataProvider('it_makes_an_argument_object_exception_provider')]
    public function it_makes_an_argument_object_exception(
        string|object $pathOrObject,
        string $method,
        int $position,
        string $expected,
        bool $nullable,
        mixed $value,
        string $message,
    ): void {
        $exception = $this->factory->makeArgumentObject(
            $pathOrObject,
            $method,
            $position,
            $expected,
            $nullable,
            $value,
        );

        $this->assertInstanceOf(Archipelago_TypeException::class, $exception);
        $this->assertSame($message, $exception->getMessage());
    }

    /**
     * @return array<string, ArgumentScenario>
     */
    public static function it_makes_an_argument_object_exception_provider(): array
    {
        return [
            '$pathOrObject is class string, got string, not nullable' => [
                'pathOrObject' => 'Some_Class',
                'method' => 'someMethod',
                'position' => 1,
                'expected' => 'Some_Other_Class',
                'nullable' => false,
                'value' => 'string',
                'message' => 'Some_Class::someMethod() argument 1 should be an instance of '
                    . 'Some_Other_Class, got string.',
            ],
            '$pathOrObject is class string, got string, nullable' => [
                'pathOrObject' => 'Some_Class',
                'method' => 'someMethod',
                'position' => 1,
                'expected' => 'Some_Other_Class',
                'nullable' => true,
                'value' => 'string',
                'message' => 'Some_Class::someMethod() argument 1 should be an instance of '
                    . 'Some_Other_Class or null, got string.',
            ],
            '$pathOrObject is object, got string, not nullable' => [
                'pathOrObject' => new stdClass(),
                'method' => 'someMethod',
                'position' => 1,
                'expected' => 'Some_Other_Class',
                'nullable' => false,
                'value' => 'string',
                'message' => 'stdClass::someMethod() argument 1 should be an instance of '
                    . 'Some_Other_Class, got string.',
            ],
            '$pathOrObject is object, got string, nullable' => [
                'pathOrObject' => new stdClass(),
                'method' => 'someMethod',
                'position' => 1,
                'expected' => 'Some_Other_Class',
                'nullable' => true,
                'value' => 'string',
                'message' => 'stdClass::someMethod() argument 1 should be an instance of '
                    . 'Some_Other_Class or null, got string.',
            ],
            '$pathOrObject is class string, position 3' => [
                'pathOrObject' => 'Some_Class',
                'method' => 'someMethod',
                'position' => 3,
                'expected' => 'Some_Other_Class',
                'nullable' => false,
                'value' => 'string',
                'message' => 'Some_Class::someMethod() argument 3 should be an instance of '
                    . 'Some_Other_Class, got string.',
            ],
        ];
    }

    #[Test]
    #[DataProvider('it_makes_an_argument_non_object_exception_provider')]
    public function it_makes_an_argument_non_object_exception(
        string|object $pathOrObject,
        string $method,
        int $position,
        string $expected,
        bool $nullable,
        mixed $value,
        string $message,
    ): void {
        $exception = $this->factory->makeArgumentNonObject(
            $pathOrObject,
            $method,
            $position,
            $expected,
            $nullable,
            $value,
        );

        $this->assertInstanceOf(Archipelago_TypeException::class, $exception);
        $this->assertSame($message, $exception->getMessage());
    }

    /**
     * @return array<string, ArgumentScenario>
     */
    public static function it_makes_an_argument_non_object_exception_provider(): array
    {
        return [
            '$pathOrObject is class string, got int, not nullable' => [
                'pathOrObject' => 'Some_Class',
                'method' => 'someMethod',
                'position' => 1,
                'expected' => 'string',
                'nullable' => false,
                'value' => 1,
                'message' => 'Some_Class::someMethod() argument 1 should be string, got int.',
            ],
            '$pathOrObject is class string, got int, nullable' => [
                'pathOrObject' => 'Some_Class',
                'method' => 'someMethod',
                'position' => 1,
                'expected' => 'string',
                'nullable' => true,
                'value' => 1,
                'message' => 'Some_Class::someMethod() argument 1 should be string or null, got int.',
            ],
            '$pathOrObject is object, got int, not nullable' => [
                'pathOrObject' => new stdClass(),
                'method' => 'someMethod',
                'position' => 1,
                'expected' => 'string',
                'nullable' => false,
                'value' => 1,
                'message' => 'stdClass::someMethod() argument 1 should be string, got int.',
            ],
            '$pathOrObject is object, got int, nullable' => [
                'pathOrObject' => new stdClass(),
                'method' => 'someMethod',
                'position' => 1,
                'expected' => 'string',
                'nullable' => true,
                'value' => 1,
                'message' => 'stdClass::someMethod() argument 1 should be string or null, got int.',
            ],
            '$pathOrObject is class string, position 2' => [
                'pathOrObject' => 'Some_Class',
                'method' => 'someMethod',
                'position' => 2,
                'expected' => 'string',
                'nullable' => false,
                'value' => 1,
                'message' => 'Some_Class::someMethod() argument 2 should be string, got int.',
            ],
        ];
    }

    #[Test]
    #[DataProvider('it_makes_a_return_object_exception_provider')]
    public function it_makes_a_return_object_exception(
        string|object $pathOrObject,
        string $method,
        string $expected,
        bool $nullable,
        mixed $value,
        string $message,
    ): void {
        $exception = $this->factory->makeReturnObject(
            $pathOrObject,
            $method,
            $expected,
            $nullable,
            $value,
        );

        $this->assertInstanceOf(Archipelago_TypeException::class, $exception);
        $this->assertSame($message, $exception->getMessage());
    }

    /**
     * @return array<string, ReturnScenario>
     */
    public static function it_makes_a_return_object_exception_provider(): array
    {
        return [
            '$pathOrObject is class string, got string, not nullable' => [
                'pathOrObject' => 'Some_Class',
                'method' => 'someMethod',
                'expected' => 'Some_Other_Class',
                'nullable' => false,
                'value' => 'string',
                'message' => 'Some_Class::someMethod() should return an instance of '
                    . 'Some_Other_Class, got string.',
            ],
            '$pathOrObject is class string, got string, nullable' => [
                'pathOrObject' => 'Some_Class',
                'method' => 'someMethod',
                'expected' => 'Some_Other_Class',
                'nullable' => true,
                'value' => 'string',
                'message' => 'Some_Class::someMethod() should return an instance of '
                    . 'Some_Other_Class or null, got string.',
            ],
            '$pathOrObject is object, got string, not nullable' => [
                'pathOrObject' => new stdClass(),
                'method' => 'someMethod',
                'expected' => 'Some_Other_Class',
                'nullable' => false,
                'value' => 'string',
                'message' => 'stdClass::someMethod() should return an instance of '
                    . 'Some_Other_Class, got string.',
            ],
            '$pathOrObject is object, got string, nullable' => [
                'pathOrObject' => new stdClass(),
                'method' => 'someMethod',
                'expected' => 'Some_Other_Class',
                'nullable' => true,
                'value' => 'string',
                'message' => 'stdClass::someMethod() should return an instance of '
                    . 'Some_Other_Class or null, got string.',
            ],
        ];
    }

    #[Test]
    #[DataProvider('it_makes_a_return_non_object_exception_provider')]
    public function it_makes_a_return_non_object_exception(
        string|object $pathOrObject,
        string $method,
        string $expected,
        bool $nullable,
        mixed $value,
        string $message,
    ): void {
        $exception = $this->factory->makeReturnNonObject(
            $pathOrObject,
            $method,
            $expected,
            $nullable,
            $value,
        );

        $this->assertInstanceOf(Archipelago_TypeException::class, $exception);
        $this->assertSame($message, $exception->getMessage());
    }

    /**
     * @return array<string, ReturnScenario>
     */
    public static function it_makes_a_return_non_object_exception_provider(): array
    {
        return [
            '$pathOrObject is class string, got int, not nullable' => [
                'pathOrObject' => 'Some_Class',
                'method' => 'someMethod',
                'expected' => 'string',
                'nullable' => false,
                'value' => 1,
                'message' => 'Some_Class::someMethod() should return string, got int.',
            ],
            '$pathOrObject is class string, got int, nullable' => [
                'pathOrObject' => 'Some_Class',
                'method' => 'someMethod',
                'expected' => 'string',
                'nullable' => true,
                'value' => 1,
                'message' => 'Some_Class::someMethod() should return string or null, got int.',
            ],
            '$pathOrObject is object, got int, not nullable' => [
                'pathOrObject' => new stdClass(),
                'method' => 'someMethod',
                'expected' => 'string',
                'nullable' => false,
                'value' => 1,
                'message' => 'stdClass::someMethod() should return string, got int.',
            ],
            '$pathOrObject is object, got int, nullable' => [
                'pathOrObject' => new stdClass(),
                'method' => 'someMethod',
                'expected' => 'string',
                'nullable' => true,
                'value' => 1,
                'message' => 'stdClass::someMethod() should return string or null, got int.',
            ],
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = new Archipelago_Internal_ExceptionFactory_TypeExceptionFactory();
    }
}
