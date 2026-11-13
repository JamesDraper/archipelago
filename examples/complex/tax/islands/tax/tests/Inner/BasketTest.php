<?php
declare(strict_types=1);

namespace Examples\Complex\Tax\Tests\Inner;

use Examples\Complex\Islands\Tax\Inner\Basket;
use Examples\Complex\Islands\Tax\Inner\Item;

use InvalidArgumentException;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class BasketTest extends TestCase
{
    #[Test]
    public function net_is_the_sum_of_the_item_prices(): void
    {
        $this->assertSame(1150, (new Basket([new Item(350), new Item(800)]))->net());
    }

    #[Test]
    public function an_empty_basket_has_no_net(): void
    {
        $this->assertSame(0, (new Basket([]))->net());
    }

    #[Test]
    public function an_item_cannot_have_a_negative_price(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Item(-1);
    }
}
