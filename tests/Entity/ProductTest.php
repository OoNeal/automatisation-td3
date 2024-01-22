<?php

namespace Tests\Entity;

use PHPUnit\Framework\TestCase;
use App\Entity\Product;
use Faker\Factory as FakerFactory;
use Faker\Generator;

class ProductTest extends TestCase
{
    private Generator $faker;
    private Product $product;

    private string $productName;

    private array $productPrices;

    public function setUp(): void
    {
        $this->faker = FakerFactory::create();
        $this->productName = $this->faker->name;
        $productPrice = $this->faker->randomFloat(2, 1, 1000);
        $this->productPrices['USD'] = $productPrice;
        $productPrice = $this->faker->randomFloat(2, 1, 1000);
        $this->productPrices['EUR'] = $productPrice;

        $this->faker->randomElement(Product::AVAILABLE_TYPES);
        $this->product = new Product($this->productName, $this->productPrices, $this->faker->randomElement(Product::AVAILABLE_TYPES));
        parent::setUp();
    }

    public function testGetName(): void
    {
        $product = new Product('Product 1', ['USD' => 10.0, 'EUR' => 8.0], 'food');
        $this->assertEquals('Product 1', $product->getName());
    }

    public function testSetName(): void
    {
        $nameProduct = $this->faker->name;
        $this->product->setName($nameProduct);
        $this->assertEquals($nameProduct, $this->product->getName());
    }

    public function testGetPrices(): void
    {
        $product = new Product('Product 1', ['USD' => 10.0, 'EUR' => 8.0], 'food');
        $this->assertEquals(['USD' => 10.0, 'EUR' => 8.0], $product->getPrices());
    }

    public function testGetType(): void
    {
        $product = new Product('Product 1', ['USD' => 10.0, 'EUR' => 8.0], 'food');
        $this->assertEquals('food', $product->getType());
    }

    public function testSetType(): void
    {
        $this->product->setType('tech');
        $this->assertEquals('tech', $this->product->getType());
    }

    public function testSetInvalidType(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid type');

        $product = new Product('Product 1', ['USD' => 10.0, 'EUR' => 8.0], 'invalid_type');
    }

    public function testSetPrices(): void
    {
        $this->product->setPrices(['USD' => 10.0, 'EUR' => 8.0, 'JPY' => 1000.0]);
        $this->assertEquals(['USD' => 10.0, 'EUR' => 8.0], $this->product->getPrices());
    }

    public function testSetInvalidCurrency(): void
    {
        $this->product->setPrices(['XYZ' => 10.0]);
        $this->assertEquals($this->productPrices, $this->product->getPrices());
    }

    public function testSetNegativePrice(): void
    {
        $this->product->setPrices(['USD' => -10.0]);
        $this->assertEquals($this->productPrices, $this->product->getPrices());
    }

    public function testGetTVA(): void
    {
        $foodProduct = new Product('Food Product', ['USD' => 10.0], 'food');
        $techProduct = new Product('Tech Product', ['USD' => 10.0], 'tech');

        $this->assertEquals(0.1, $foodProduct->getTVA());
        $this->assertEquals(0.2, $techProduct->getTVA());
    }

    public function testListCurrencies(): void
    {
        $this->assertEquals(['USD', 'EUR'], $this->product->listCurrencies());
    }

    public function testGetPrice(): void
    {
        $product = new Product('Product 1', ['USD' => 10.0, 'EUR' => 8.0], 'food');
        $this->assertEquals(10.0, $product->getPrice('USD'));
    }

    public function testGetPriceInvalidCurrency(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid currency');

        $this->product->getPrice('XYZ');
    }

    public function testGetPriceCurrencyNotAvailable(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Currency not available for this product');

        $product = new Product('Product 1', ['EUR' => 8.0], 'food');
        $product->getPrice('USD');
    }
}
