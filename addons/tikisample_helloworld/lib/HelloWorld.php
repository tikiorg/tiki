<?php

namespace tikiaddon\tikisample\helloworld;

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
