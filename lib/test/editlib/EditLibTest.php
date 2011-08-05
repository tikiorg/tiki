<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: ParserTest.php 33195 2011-03-02 17:43:40Z changi67 $

class EditLibTest extends TikiTestCase {
	
	function testParseStyleAttribute() {
		
		$el = new EditLib();
		
		/*
		 * empty style -> empty array
		 */
		$style = '';
		$parsed = array();
		$el->parseStyleAttribute($style, $parsed);
		$this->assertEquals(0, count($parsed));
		var_dump($parsed);
		

		/*
		 * delimiters only -> empty array
		 */
		$style = ' ; ; ';
		$parsed = array();
		$el->parseStyleAttribute($style, $parsed);
		$this->assertEquals(0, count($parsed));
		
		
		/*
		 * example
		 */
		$style = 'background:rgb(1,2,3) url(background.gif);font-size:12;';
		$parsed = array();
		$el->parseStyleAttribute($style, $parsed);
		$this->assertEquals(2, count($parsed));
		$this->assertTrue(isset($parsed['background']));
		$this->assertEquals('rgb(1,2,3) url(background.gif)', $parsed['background']);
		$this->assertTrue(isset($parsed['font-size']));
		$this->assertEquals(12, $parsed['font-size']);
		

		/*
		 * example with no trailing ';'
		 */
		$style = 'background:rgb(1,2,3) url(background.gif);font-size:12';
		$parsed = array();
		$el->parseStyleAttribute($style, $parsed);
		$this->assertEquals(2, count($parsed));
		$this->assertTrue(isset($parsed['background']));
		$this->assertEquals('rgb(1,2,3) url(background.gif)', $parsed['background']);
		$this->assertTrue(isset($parsed['font-size']));
		$this->assertEquals(12, $parsed['font-size']);
		
		
		/*
		 * example with unrequired spaces
		 */
		$style = ' background : rgb( 1 , 2 , 3 ) url( background.gif ) ; font-size: 12 ; ';
		$parsed = array();
		$el->parseStyleAttribute($style, $parsed);
		$this->assertEquals(2, count($parsed));
		$this->assertTrue(isset($parsed['background']));
		$this->assertEquals('rgb( 1 , 2 , 3 ) url( background.gif )', $parsed['background']);
		$this->assertTrue(isset($parsed['font-size']));
		$this->assertEquals(12, $parsed['font-size']);		
	}
	
}