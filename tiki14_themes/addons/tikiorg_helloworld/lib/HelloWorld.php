<?php

namespace tikiaddon\tikiorg\helloworld;

class Foo
{
	private $message = '';

	function __construct()
	{
		$this->message = "Hello World!";
	}

	function hello()
	{
		return $this->message;
	}
}
