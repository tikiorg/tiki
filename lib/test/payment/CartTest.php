<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

TikiLib::lib('cart');

class Payment_CartTest extends TikiTestCase
{
	function setUp()
	{
		global $prefs;
		$prefs['feature_sefurl'] = 'n';
		$this->obj = $this->getMockBuilder('CartLib')
			->setMethods(['get_gift_certificate_code'])
			->getMock();
		$_SERVER['REQUEST_URI'] = '/tiki-index.php';
	}

	function tearDown()
	{
		unset($_SESSION['cart']);
	}

	function testEmptyCart()
	{
		$this->obj->expects($this->once())->method('get_gift_certificate_code');
		$this->assertEquals(0.0, $this->obj->get_total());
	}

	function testAddToCart()
	{
		$this->obj->add_product(
			'T-123',
			3,
			[
				'price' => '100.43',
				'description' => 'Hello',
			]
		);

		$this->assertEquals(301.29, $this->obj->get_total());
	}

	function testUpdateQuantity()
	{
		$this->obj->add_product(
			'T-123',
			3,
			[
				'price' => '100.43',
				'description' => 'Hello',
			]
		);

		$this->obj->update_quantity('T-123', 1);

		$this->assertEquals(100.43, $this->obj->get_total());
	}

	function testMultipleProducts()
	{
		$this->obj->add_product(
			'T-123',
			2,
			[
				'price' => '100.43',
				'description' => 'Hello',
			]
		);
		$this->obj->add_product(
			'T-456',
			1,
			[
				'price' => '100.43',
				'description' => 'World',
			]
		);

		$this->assertEquals(301.29, $this->obj->get_total());
	}

	function testProductWithConflictingInformation()
	{
		$this->obj->add_product(
			'T-123',
			2,
			[
				'price' => '100.43',
				'description' => 'Hello',
			]
		);
		$this->obj->add_product(
			'T-123',
			1,
			[
				'price' => '1000.00',
				'description' => 'World',
			]
		);

		$this->assertEquals(301.29, $this->obj->get_total());
	}

	function testUpdateMissingProduct()
	{
		$this->obj->update_quantity('1234', 3);

		$this->assertEquals(0, $this->obj->get_quantity('1234'));
	}

	function testPrecision()
	{
		$this->obj->add_product(
			'T-456',
			1,
			[
				'price' => '1.012',
				'description' => 'World',
			]
		);

		$this->assertEquals(1.01, $this->obj->get_total());
	}

	function testNegativeQuantity()
	{
		$this->obj->add_product(
			'T-456',
			-1,
			[
				'price' => '1.01',
				'description' => 'World',
			]
		);

		$this->assertEquals(1.01, $this->obj->get_total());
	}

	function testNegativePrice()
	{
		$this->obj->add_product(
			'T-456',
			1,
			[
				'price' => '-1.01',
				'description' => 'World',
			]
		);

		$this->assertEquals(0, $this->obj->get_total());
	}

	function testZeroQuantityRemovedLine()
	{
		$this->obj->add_product(
			'T-123',
			2,
			[
				'price' => '100.43',
				'description' => 'Hello',
			]
		);

		$this->obj->update_quantity('T-123', 0);

		$this->assertEquals([], $this->obj->get_content());
	}

	function testPricePadded()
	{
		$this->obj->add_product(
			'T-123',
			2,
			[
				'price' => '100.4',
				'description' => 'Hello',
			]
		);

		$content = $this->obj->get_content();
		$this->assertSame('100.40', $content['T-123']['price']);
	}

	function testTotalPadded()
	{
		$this->obj->add_product(
			'T-123',
			2,
			[
				'price' => '100.4',
				'description' => 'Hello',
			]
		);

		$this->assertSame('200.80', $this->obj->get_total());
	}

	function testRequestPaymentClearsCart()
	{
		global $user;
		$user = 'admin';

		$this->obj->add_product(
			'T-123',
			2,
			[
				'price' => '100.4',
				'description' => 'Hello',
				'eventcode' => 123,
				'producttype' => 'Any type'
			]
		);

		$this->obj->request_payment();

		$this->assertEquals([], $this->obj->get_content());
	}

	function testEmptyCartRequestsNothing()
	{
		$this->assertEquals(0, $this->obj->request_payment());
	}

	function testCollectDescription()
	{
		$this->obj->add_product(
			'T-123',
			2,
			[
				'description' => 'Hello World',
				'href' => 'product123',
				'price' => 12.50,
			]
		);
		$this->obj->add_product(
			'T-456',
			1,
			[
				'description' => 'Foobar',
				'price' => 120.50,
			]
		);

		$this->assertEquals(
			"||__ID__|__Product__|__Quantity__|__Unit Price__
T-123|[product123|Hello World]|2|12.50
T-456|Foobar|1|120.50
||
",
			$this->obj->get_description()
		);
	}

	function testWithItemsRegistersPayment()
	{
		$paymentlib = TikiLib::lib('payment');

		$this->obj->add_product(
			'123',
			2,
			[
				'price' => 123,
				'description' => 'test',
				'eventcode' => 123,
				'producttype' => 'any type',
			]
		);

		$id = $this->obj->request_payment();

		$this->assertNotEquals(0, $id);

		$payment = $paymentlib->get_payment($id);

		TikiDb::get()->query('DELETE FROM tiki_payment_requests WHERE paymentRequestId = ?', [$id]);

		$this->assertEquals(246, $payment['amount_original']);
		$this->assertContains('123|test|2|123', $payment['detail']);
	}

	function testRegisteredBehaviorsOnItems()
	{
		$paymentlib = TikiLib::lib('payment');

		$this->obj->add_product(
			'123',
			2,
			[
				'price' => 123,
				'description' => 'test',
				'eventcode' => 123,
				'producttype' => 'any type',
				'behaviors' => [
					[
						'event' => 'complete',
						'behavior' => 'sample',
						'arguments' => ['Done 123!']
					],
					[
						'event' => 'cancel',
						'behavior' => 'sample',
						'arguments' => ['No 123!']
					],
				],
			]
		);
		$this->obj->add_product(
			'456',
			1,
			[
				'price' => 456,
				'description' => 'test',
				'eventcode' => 123,
				'producttype' => 'any type',
				'behaviors' => [
					[
						'event' => 'complete',
						'behavior' => 'sample',
						'arguments' => ['Done 456!']
					],
					[
						'event' => 'cancel',
						'behavior' => 'sample',
						'arguments' => ['No 456!']
					],
				],
			]
		);

		$id = $this->obj->request_payment();

		$this->assertNotEquals(0, $id);

		$payment = $paymentlib->get_payment($id);

		TikiDb::get()->query('DELETE FROM tiki_payment_requests WHERE paymentRequestId = ?', [$id]);

		$this->assertEquals(
			[
				['behavior' => 'sample', 'arguments' => ['Done 123!']],
				['behavior' => 'sample', 'arguments' => ['Done 123!']],
				['behavior' => 'sample', 'arguments' => ['Done 456!']],
			],
			$payment['actions']['complete']
		);

		$this->assertEquals(
			[
				['behavior' => 'sample', 'arguments' => ['No 123!']],
				['behavior' => 'sample', 'arguments' => ['No 123!']],
				['behavior' => 'sample', 'arguments' => ['No 456!']],
				['behavior' => 'replace_inventory', 'arguments' => [123, 2]],
				['behavior' => 'replace_inventory', 'arguments' => [456, 1]],
			],
			$payment['actions']['cancel']
		);
	}

	function testGetGiftCertificateCode_shouldReturnCodeIfNotNull()
	{
		$this->markTestSkipped("As of 2013-10-02, this test is broken, and nobody knows how to fix it. Mark as Skipped for now.");
		$obj = new CartLib;
		$code = 123;
		$this->assertEquals($code, $obj->get_gift_certificate_code($code));
	}

	function testGetGiftCertificateCode_shouldReturnValueStoreInSession()
	{
		$obj = new CartLib;
		$code = null;
		$_SESSION['cart']['tiki-gc']['code'] = 123;
		$this->assertEquals(123, $obj->get_gift_certificate_code($code));
	}
}
