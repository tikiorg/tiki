<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/** 
 * @group unit
 * 
 */

class Perms_MixedTest extends TikiTestCase
{
	function testFilterMixed() {
		$perms = new Perms;
		$perms->setResolverFactories( array(
			$resolver = $this->getMock( 'Perms_ResolverFactory' ),
			new Perms_ResolverFactory_StaticFactory( 'global', new Perms_Resolver_Default( true ) ),
		) );
		Perms::set( $perms );

		$resolver->expects( $this->any() )
			->method( 'getResolver' )
			->will( $this->returnValue( null ) );
		$resolver->expects( $this->exactly( 3 ) )
			->method( 'bulk' )
			->will( $this->returnValue( array() ) );
		$resolver->expects( $this->at( 0 ) )
			->method( 'bulk' )
			->will( $this->returnValue( array() ) )
			->with(
				$this->equalTo( array( 'type' => 'wiki page' ) ),
				$this->equalTo( 'object' ),
				$this->equalTo( array( 'A', 'B' ) )
			);
		$resolver->expects( $this->at( 1 ) )
			->method( 'bulk' )
			->will( $this->returnValue( array() ) )
			->with(
				$this->equalTo( array( 'type' => 'category' ) ),
				$this->equalTo( 'object' ),
				$this->equalTo( array( 10 ) )
			);

		$data = array(
			array( 'type' => 'wiki page', 'object' => 'A', 'creator' => 'abc' ),
			array( 'type' => 'wiki page', 'object' => 'B', 'creator' => 'abc' ),
			array( 'type' => 'category', 'object' => 10 ),
			array( 'type' => 'forumPost', 'object' => 12, 'author' => 'author' ),
		);

		$out = Perms::mixedFilter( array(), 'type', 'object', $data, array(
			'wiki page' => array( 'object' => 'object', 'type' => 'type', 'creator' => 'creator' ),
			'category' => array( 'object' => 'object', 'type' => 'type' ),
			'forumPost' => array( 'object' => 'object', 'type' => 'type', 'creator' => 'author' ),
		),
		array(
			'wiki page' => 'view',
			'category' => 'view_categories',
			'forumPost' => 'forum_post',
		) );

		$this->assertEquals( $data, $out );
	}
}
