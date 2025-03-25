<?php

declare(strict_types=1);

namespace App\Tests\PriceList\Application\Command\CreatePriceListCommand;

use App\PriceList\Application\Command\CreatePriceListCommand\CreatePriceListCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class CreatePriceListCommandTest extends TestCase
{
    private Uuid $id;
    private string $name;
    private string $currency;
    private CreatePriceListCommand $command;

    protected function setUp(): void
    {
        $this->id = Uuid::v4();
        $this->name = 'Test Price List';
        $this->currency = 'USD';

        $this->command = new CreatePriceListCommand(
            id: $this->id,
            name: $this->name,
            currency: $this->currency
        );
    }

    public function testCommandPropertiesAreCorrectlySet(): void
    {
        $this->assertEquals($this->id, $this->command->id);
        $this->assertEquals($this->name, $this->command->name);
        $this->assertEquals($this->currency, $this->command->currency);
    }
}
