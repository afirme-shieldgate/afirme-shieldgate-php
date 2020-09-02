<?php

use Payment\Exceptions\{PaymentErrorException, RequestException};
use Payment\Payment;
use PHPUnit\Framework\TestCase;

final class CardTest extends TestCase
{
    protected $service;

    public function setUp()
    {
        Payment::init("MAGENTO_MX_SERVER", "DKzCAv6EXXgQrC0hATOltXZ6OZ7Zss");
        $this->service = Payment::card();
    }

    public function testSuccessCardList()
    {
        $list = $this->service->getList(1);
        $this->assertIsObject($list);
        $this->assertTrue(($list instanceof \stdClass));
        $this->assertIsNumeric($list->result_size);
        $this->assertIsArray($list->cards);
    }

    public function testFailParamsCardList()
    {
        $this->expectException(RequestException::class);
        $this->service->getList("randomUID");
    }

    public function testFailCardList()
    {
        $this->expectException(PaymentErrorException::class);
        Payment::init("1", "s");
        $service = Payment::card();
        $service->getList("1");
    }
}
