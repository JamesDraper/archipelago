<?php
declare(strict_types=1);

namespace Examples\Complex\Tax\Tests\Inner;

use Examples\Complex\Islands\Tax\Inner\TaxRate;
use Examples\Complex\Islands\Tax\Inner\Ingress;
use Examples\Complex\Islands\Tax\Inner\Request;
use Examples\Complex\Islands\Tax\Inner\Basket;
use Examples\Complex\Islands\Tax\Inner\Item;

use Archipelago_Island_Inner_Request;

use InvalidArgumentException;
use LogicException;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use PHPUnit\Framework\Attributes\Test;
use Mockery\MockInterface;
use Mockery;

class IngressTest extends MockeryTestCase
{
    #[Test]
    public function it_totals_the_basket_and_adds_the_tax(): void
    {
        $taxRate = $this->mockTaxRate();
        $taxRate->shouldReceive('of')->once()->with(1150)->andReturn(230);

        $response = $this->makeIngress($taxRate)->invoke(new Request(new Basket([new Item(350), new Item(800)])));

        $this->assertSame(1150, $response->totals->net);
        $this->assertSame(230, $response->totals->tax);
        $this->assertSame(1380, $response->totals->gross);
    }

    #[Test]
    public function an_empty_basket_totals_zero(): void
    {
        $taxRate = $this->mockTaxRate();
        $taxRate->shouldReceive('of')->once()->with(0)->andReturn(0);

        $response = $this->makeIngress($taxRate)->invoke(new Request(new Basket([])));

        $this->assertSame(0, $response->totals->net);
        $this->assertSame(0, $response->totals->tax);
        $this->assertSame(0, $response->totals->gross);
    }

    #[Test]
    public function it_fails_when_not_configured(): void
    {
        $this->expectException(LogicException::class);

        (new Ingress())->invoke(new Request(new Basket([])));
    }

    #[Test]
    public function it_rejects_an_unknown_request(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->makeIngress($this->mockTaxRate())->invoke(new class implements Archipelago_Island_Inner_Request {});
    }

    /**
     * @return TaxRate&MockInterface
     */
    private function mockTaxRate(): TaxRate
    {
        return Mockery::mock(TaxRate::class);
    }

    /**
     * The configurator's setter is the seam: tests inject what it would.
     */
    private function makeIngress(TaxRate $taxRate): Ingress
    {
        $ingress = new Ingress();
        $ingress->setTaxRate($taxRate);

        return $ingress;
    }
}
