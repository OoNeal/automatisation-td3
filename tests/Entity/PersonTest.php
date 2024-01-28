<?php

namespace Tests\Entity;

use App\Entity\Person;
use App\Entity\Product;
use App\Entity\Wallet;
use Faker\Factory as FakerFactory;
use Faker\Generator;
use PHPUnit\Framework\TestCase;

class PersonTest extends TestCase
{
    private Generator $faker;

    public function setUp(): void
    {
        parent::setUp();
        $this->faker = FakerFactory::create();
    }

    private function createRandomPerson(): Person
    {
        $name = $this->faker->name;
        $currency = $this->faker->randomElement(Wallet::AVAILABLE_CURRENCY);
        return new Person($name, $currency);
    }

    public function testGetName(): void
    {
        $name = $this->faker->name;
        $person = new Person($name, $this->faker->randomElement(Wallet::AVAILABLE_CURRENCY));
        $this->assertEquals($name, $person->getName());
    }

    public function testSetName(): void
    {
        $person = $this->createRandomPerson();
        $newName = $this->faker->name;
        $person->setName($newName);
        $this->assertEquals($newName, $person->getName());
    }

    public function testGetWallet(): void
    {
        $person = $this->createRandomPerson();
        $wallet = new Wallet($this->faker->randomElement(Wallet::AVAILABLE_CURRENCY));
        $person->setWallet($wallet);
        $this->assertEquals($wallet, $person->getWallet());
    }

    public function testSetWallet(): void
    {
        $person = $this->createRandomPerson();
        $wallet = new Wallet($this->faker->randomElement(Wallet::AVAILABLE_CURRENCY));
        $person->setWallet($wallet);
        $this->assertEquals($wallet, $person->getWallet());
    }

    public function testHasFund(): void
    {
        $person = $this->createRandomPerson();
        $wallet = new Wallet($this->faker->randomElement(Wallet::AVAILABLE_CURRENCY));
        $initialBalance = $this->faker->randomFloat(2, 1, 1000);
        $wallet->setBalance($initialBalance);
        $person->setWallet($wallet);
        $this->assertTrue($person->hasFund());

        $wallet->removeFund($initialBalance);
        $this->assertFalse($person->hasFund());
    }

    public function testTransfertFund(): void
    {
        $person1 = new Person($this->faker->name, 'EUR');
        $person2 = new Person($this->faker->name, 'EUR');
        $initialBalance = $this->faker->randomFloat(2, 1, 1000);
        $person1->getWallet()->setBalance($initialBalance);

        $amountToTransfer = $this->faker->randomFloat(2, 1, $initialBalance);
        $person1->transfertFund($amountToTransfer, $person2);

        $this->assertEquals($initialBalance - $amountToTransfer, $person1->getWallet()->getBalance());
        $this->assertEquals($amountToTransfer, $person2->getWallet()->getBalance());
    }

    public function testTransfertFundWithInvalidCurrency(): void
    {
        $person1 = new Person($this->faker->name, 'USD');
        $person2 = new Person($this->faker->name, 'EUR');
        $initialBalance = $this->faker->randomFloat(2, 1, 1000);
        $person1->getWallet()->setBalance($initialBalance);

        $amountToTransfer = $this->faker->randomFloat(2, 1, $initialBalance);
        $this->expectException(\Exception::class);
        $person1->transfertFund($amountToTransfer, $person2);
    }

    public function testDivideWallet(): void
    {
        $person1 = new Person($this->faker->name, 'EUR');
        $person2 = new Person($this->faker->name, 'EUR');
        $persons = [$person1, $person2];

        $initialBalance = $this->faker->randomFloat(2, 1, 1000);
        $person1->getWallet()->setBalance($initialBalance);
        $person1->divideWallet($persons);

        $partPerPerson = round($initialBalance / count($persons), 2);

        $this->assertEquals($initialBalance - $partPerPerson, $person1->getWallet()->getBalance());

        $this->assertEquals($partPerPerson, $person2->getWallet()->getBalance());

    }

    public function testBuyProduct(): void
    {
        $person = $this->createRandomPerson();
        $walletCurrency = $this->faker->randomElement(Wallet::AVAILABLE_CURRENCY);
        $wallet = new Wallet($walletCurrency);
        $initialBalance = $this->faker->randomFloat(2, 1, 1000);
        $wallet->setBalance($initialBalance);
        $person->setWallet($wallet);

        $product = $this->createMock(Product::class);
        $product->method('listCurrencies')->willReturn([$walletCurrency]);
        $product->method('getPrice')->willReturn($this->faker->randomFloat(2, 1, 100));

        $person->buyProduct($product);

        $this->assertEquals($initialBalance - $product->getPrice('USD'), $person->getWallet()->getBalance());
    }

    public function testBuyProductWithInvalidCurrency(): void
    {
        $person = $this->createRandomPerson();
        $wallet = new Wallet('USD');
        $initialBalance = $this->faker->randomFloat(2, 1, 1000);
        $wallet->setBalance($initialBalance);
        $person->setWallet($wallet);

        $product = $this->createMock(Product::class);
        $product->method('listCurrencies')->willReturn(['EUR']);
        $product->method('getPrice')->willReturn($this->faker->randomFloat(2, 1, 100));

        $this->expectException(\Exception::class);
        $person->buyProduct($product);
    }
}
