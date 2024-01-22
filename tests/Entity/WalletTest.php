<?php

namespace Tests\Entity;

use Faker\Factory as FakerFactory;
use Faker\Generator;
use PHPUnit\Framework\TestCase;
use App\Entity\Wallet;

class WalletTest extends TestCase
{
    private Generator $faker;
    private Wallet $wallet;

    private string $currency;

    public function setUp(): void
    {
        parent::setUp();
        $this->faker = FakerFactory::create();
        $this->currency = $this->faker->randomElement(Wallet::AVAILABLE_CURRENCY);
        $this->wallet = new Wallet($this->currency);
    }

    public function testGetBalance(): void
    {
        $this->assertEquals(0.0, $this->wallet->getBalance());
    }

    public function testGetCurrency(): void
    {
        $this->assertEquals($this->currency, $this->wallet->getCurrency());
    }

    public function testSetBalance(): void
    {
        $balance = $this->faker->randomFloat(2, 1, 1000);
        $this->wallet->setBalance($balance);
        $this->assertEquals($balance, $this->wallet->getBalance());
    }

    public function testSetInvalidBalance(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid balance');

        $balance = $this->faker->randomFloat(2, -1000, 0);
        $this->wallet->setBalance($balance);
    }

    public function testSetCurrency(): void
    {
        $currency = $this->faker->randomElement(Wallet::AVAILABLE_CURRENCY);
        $this->wallet->setCurrency($currency);
        $this->assertEquals($currency, $this->wallet->getCurrency());
    }

    public function testSetInvalidCurrency(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid currency');

        $currency = $this->faker->word;
        $this->wallet->setCurrency($currency);
    }

    public function testRemoveFund(): void
    {
        $initialBalance = $this->faker->randomFloat(2, 1, 1000);
        $this->wallet->setBalance($initialBalance);

        $amountToRemove = $this->faker->randomFloat(2, 1, $initialBalance);
        $this->wallet->removeFund($amountToRemove);

        $this->assertEquals($initialBalance - $amountToRemove, $this->wallet->getBalance());
    }

    public function testRemoveInvalidAmount(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid amount');

        $amountToRemove = $this->faker->randomFloat(2, -1000, 0);
        $this->wallet->removeFund($amountToRemove);
    }

    public function testRemoveInsufficientFunds(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Insufficient funds');

        $initialBalance = $this->faker->randomFloat(2, 1, 1000);
        $this->wallet->setBalance($initialBalance);

        $amountToRemove = $this->faker->randomFloat(2, $initialBalance + 1, $initialBalance + 1000);
        $this->wallet->removeFund($amountToRemove);
    }

    public function testAddFund(): void
    {
        $initialBalance = $this->faker->randomFloat(2, 1, 1000);
        $this->wallet->setBalance($initialBalance);

        $amountToAdd = $this->faker->randomFloat(2, 1, 1000);
        $this->wallet->addFund($amountToAdd);

        $this->assertEquals($initialBalance + $amountToAdd, $this->wallet->getBalance());
    }

    public function testAddInvalidAmount(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid amount');

        $amountToAdd = $this->faker->randomFloat(2, -1000, 0);
        $this->wallet->addFund($amountToAdd);
    }
}
