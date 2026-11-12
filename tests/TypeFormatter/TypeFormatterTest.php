<?php
declare(strict_types=1);

namespace Tests\TypeFormatter;

use Archipelago_Internal_TypeFormatter;

use Tests\TypeFormatter\Fixtures\SimpleBackedEnum;
use Tests\TypeFormatter\Fixtures\SimpleEnum;
use Tests\TestCase;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

use function class_exists;
use function unserialize;
use function get_class;
use function sprintf;
use function tmpfile;
use function fclose;

/**
 * @phpstan-type Scenario array{
 *     expected: string,
 *     value: mixed
 * }
 */
final class TypeFormatterTest extends TestCase
{
    #[Test]
    #[DataProvider('provider_it_formats')]
    public function it_formats(string $expected, mixed $value): void
    {
        $this->assertSame($expected, Archipelago_Internal_TypeFormatter::format($value));
    }

    /**
     * @return array<string, Scenario>
     */
    public static function provider_it_formats(): array
    {
        return [
            'null' => [
                'expected' => 'null',
                'value' => null,
            ],
            'true' => [
                'expected' => 'bool',
                'value' => true,
            ],
            'false' => [
                'expected' => 'bool',
                'value' => false,
            ],
            'integer' => [
                'expected' => 'int',
                'value' => 123,
            ],
            'float' => [
                'expected' => 'float',
                'value' => 1.23,
            ],
            'string' => [
                'expected' => 'string',
                'value' => 'STRING',
            ],
            'array' => [
                'expected' => 'array',
                'value' => [1, 2, 3],
            ],
            'enum' => [
                'expected' => 'enum(Tests\\TypeFormatter\\Fixtures\\SimpleEnum)',
                'value' => SimpleEnum::ONE,
            ],
            'backed enum' => [
                'expected' => 'enum(Tests\\TypeFormatter\\Fixtures\\SimpleBackedEnum)',
                'value' => SimpleBackedEnum::ONE,
            ],
        ];
    }

    #[Test]
    public function it_formats_closures(): void
    {
        $closure = static fn() => 123;

        $this->assertSame('closure', Archipelago_Internal_TypeFormatter::format($closure));
    }

    #[Test]
    public function it_formats_objects(): void
    {
        $obj = new class {};

        $this->assertSame(
            sprintf('object(%s)', get_class($obj)),
            Archipelago_Internal_TypeFormatter::format($obj),
        );
    }

    #[Test]
    public function it_formats_incomplete_objects(): void
    {
        $obj = unserialize('O:32:"Tests_Unit_Fixtures_MissingClass":0:{}');

        $this->assertSame(
            'object(__PHP_Incomplete_Class)',
            Archipelago_Internal_TypeFormatter::format($obj),
        );
    }

    #[Test]
    public function it_formats_resources(): void
    {
        $file = tmpfile();

        try {
            $this->assertSame('resource', Archipelago_Internal_TypeFormatter::format($file));
        } finally {
            fclose($file);
        }
    }

    #[Test]
    public function it_formats_closed_resources(): void
    {
        $file = tmpfile();

        fclose($file);

        $this->assertSame('resource', Archipelago_Internal_TypeFormatter::format($file));
    }
}
