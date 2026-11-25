<?php

declare(strict_types=1);

namespace Tests\Unit\Validator;

use Archipelago_Internal_Validator_NameValidator;
use Archipelago_Internal_ExceptionFactory_TypeExceptionFactory;
use Archipelago_Internal_ExceptionFactory_BadValueExceptionFactory;
use Archipelago_BadValueException;
use Archipelago_TypeException;
use Archipelago_Island;

use Tests\Unit\TestCase;

use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

use Mockery\MockInterface;
use Mockery;

use stdClass;

final class NameValidatorTest extends TestCase
{
    private Archipelago_Internal_ExceptionFactory_TypeExceptionFactory&MockInterface $typeExceptionFactory;

    private Archipelago_Internal_ExceptionFactory_BadValueExceptionFactory&MockInterface $badValueExceptionFactory;

    private Archipelago_Internal_Validator_NameValidator $validator;

    #[Test]
    #[DoesNotPerformAssertions]
    public function it_accepts_a_string(): void
    {
        $island = Mockery::mock(Archipelago_Island::class);

        $this->validator->assertValidType($island, '');
    }

    #[Test]
    #[DataProvider('it_throws_when_the_name_is_not_a_string_provider')]
    public function it_throws_when_the_name_is_not_a_string(mixed $name): void
    {
        $island = Mockery::mock(Archipelago_Island::class);

        $this->assertThrowsFactoryException($island, $name);
    }

    /**
     * @return array<string, array{name: mixed}>
     */
    public static function it_throws_when_the_name_is_not_a_string_provider(): array
    {
        return [
            'null' => ['name' => null],
            'true' => ['name' => true],
            'false' => ['name' => false],
            'int' => ['name' => 1],
            'zero' => ['name' => 0],
            'float' => ['name' => 1.5],
            'array' => ['name' => []],
            'unrelated object' => ['name' => new stdClass()],
        ];
    }

    #[Test]
    public function it_throws_when_the_name_is_a_closure(): void
    {
        $island = Mockery::mock(Archipelago_Island::class);
        $name = static function (): void {};

        $this->assertThrowsFactoryException($island, $name);
    }

    #[Test]
    #[DoesNotPerformAssertions]
    public function it_accepts_a_non_empty_name(): void
    {
        $this->validator->assertNotEmpty('a');
    }

    #[Test]
    public function it_throws_when_the_name_is_empty(): void
    {
        $this->assertThrowsBadValueFactoryException(
            'Island name cannot be empty.',
            fn() => $this->validator->assertNotEmpty(''),
        );
    }

    #[Test]
    #[DoesNotPerformAssertions]
    #[DataProvider('it_accepts_a_valid_name_provider')]
    public function it_accepts_a_valid_name(string $name): void
    {
        $this->validator->assertNameValid($name);
    }

    /**
     * @return array<string, array{name: string}>
     */
    public static function it_accepts_a_valid_name_provider(): array
    {
        return [
            'single letter' => ['name' => 'a'],
            'lowercase letters' => ['name' => 'island'],
            'letters and numbers' => ['name' => 'island2'],
            'underscore' => ['name' => 'island_name'],
            'dot' => ['name' => 'island.name'],
            'all allowed characters' => ['name' => 'a.b_c9'],
        ];
    }

    #[Test]
    #[DataProvider('it_throws_when_the_name_is_invalid_provider')]
    public function it_throws_when_the_name_is_invalid(string $name): void
    {
        $expectedMessage = 'Island name must be lowercase letters, numbers, underscores, and dots only.'
            . ' It must begin with a lowercase letter. Got "' . $name . '".';

        $this->assertThrowsBadValueFactoryException(
            $expectedMessage,
            fn() => $this->validator->assertNameValid($name),
        );
    }

    /**
     * @return array<string, array{name: string}>
     */
    public static function it_throws_when_the_name_is_invalid_provider(): array
    {
        return [
            'empty string' => ['name' => ''],
            'starts with uppercase' => ['name' => 'Island'],
            'contains uppercase' => ['name' => 'isLand'],
            'starts with number' => ['name' => '1island'],
            'starts with underscore' => ['name' => '_island'],
            'starts with dot' => ['name' => '.island'],
            'contains hyphen' => ['name' => 'island-name'],
            'contains space' => ['name' => 'island name'],
            'contains slash' => ['name' => 'island/name'],
            'leading space' => ['name' => ' island'],
            'trailing space' => ['name' => 'island '],
            'trailing newline' => ['name' => "island\n"],
            'non-ascii letter' => ['name' => 'îsland'],
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->typeExceptionFactory = Mockery::mock(Archipelago_Internal_ExceptionFactory_TypeExceptionFactory::class);
        $this->badValueExceptionFactory = Mockery::mock(Archipelago_Internal_ExceptionFactory_BadValueExceptionFactory::class);
        $this->validator = new Archipelago_Internal_Validator_NameValidator(
            $this->typeExceptionFactory,
            $this->badValueExceptionFactory,
        );
    }

    private function assertThrowsBadValueFactoryException(string $message, callable $action): void
    {
        $exception = Mockery::mock(Archipelago_BadValueException::class);

        $this->badValueExceptionFactory
            ->expects()
            ->make($message)
            ->andReturn($exception);

        try {
            $action();
        } catch (Archipelago_BadValueException $thrown) {
            $this->assertSame($exception, $thrown);

            return;
        }

        $this->fail('Expected ' . Archipelago_BadValueException::class . ' to be thrown.');
    }

    private function assertThrowsFactoryException(Archipelago_Island $island, mixed $name): void
    {
        $exception = Mockery::mock(Archipelago_TypeException::class);

        $this->typeExceptionFactory
            ->expects()
            ->makeReturnNonObject($island, 'getName', 'string', false, $name)
            ->andReturn($exception);

        try {
            $this->validator->assertValidType($island, $name);
        } catch (Archipelago_TypeException $thrown) {
            $this->assertSame($exception, $thrown);

            return;
        }

        $this->fail('Expected ' . Archipelago_TypeException::class . ' to be thrown.');
    }
}
