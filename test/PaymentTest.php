<?php

use Payment\Exceptions\{RequestException};
use Payment\Payment;
use PHPUnit\Framework\TestCase;

final class PaymentTest extends TestCase
{
    public function testInvalidResource()
    {
        $this->expectException(RequestException::class);

        Payment::init("random", "random");
        Payment::randomResource();
    }

}
