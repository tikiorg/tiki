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

class Perms_ApplierTest extends TikiTestCase
{
	function testApplyFromNothing() {
		$global = new Perms_Reflection_PermissionSet;
		$global->add( 'Anonymous', 'view' );

		$object = new Perms_Reflection_PermissionSet;

		$newSet = new Perms_Reflection_PermissionSet;
		$newSet->add( 'Registered', 'view' );
		$newSet->add( 'Registered', 'edit' );

		$target = $this->getMock( 'Perms_Reflection_Container' );
		$target->expects( $this->at(0) )
			->method( 'getDirectPermissions' )
			->will( $this->returnValue( $object ) );
		$target->expects( $this->at(1) )
			->method( 'getParentPermissions' )
			->will( $this->returnValue( $global ) );
		$target->expects( $this->at(2) )
			->method( 'add' )
			->with( $this->equalTo( 'Registered' ), $this->equalTo( 'view' ) );
		$target->expects( $this->at(3) )
			->method( 'add' )
			->with( $this->equalTo( 'Registered' ), $this->equalTo( 'edit' ) );

		$applier = new Perms_Applier;
		$applier->addObject( $target );
		$applier->apply( $newSet );
	}

	function testFromExistingSet() {
		$global = new Perms_Reflection_PermissionSet;
		$global->add( 'Anonymous', 'view' );

		$object = new Perms_Reflection_PermissionSet;
		$object->add( 'Registered', 'view' );
		$object->add( 'Registered', 'edit' );

		$newSet = new Perms_Reflection_PermissionSet;
		$newSet->add( 'Registered', 'view' );
		$newSet->add( 'Editor', 'edit' );
		$newSet->add( 'Editor', 'view_history' );

		$target = $this->getMock( 'Perms_Reflection_Container' );
		$target->expects( $this->at(0) )
			->method( 'getDirectPermissions' )
			->will( $this->returnValue( $object ) );
		$target->expects( $this->at(1) )
			->method( 'getParentPermissions' )
			->will( $this->returnValue( $global ) );
		$target->expects( $this->at(2) )
			->method( 'add' )
			->with( $this->equalTo( 'Editor' ), $this->equalTo( 'edit' ) );
		$target->expects( $this->at(3) )
			->method( 'add' )
			->with( $this->equalTo( 'Editor' ), $this->equalTo( 'view_history' ) );
		$target->expects( $this->at(4) )
			->method( 'remove' )
			->with( $this->equalTo( 'Registered' ), $this->equalTo( 'edit' ) );

		$applier = new Perms_Applier;
		$applier->addObject( $target );
		$applier->apply( $newSet );
	}

	function testAsParent() {
		$global = new Perms_Reflection_PermissionSet;
		$global->add( 'Anonymous', 'view' );

		$object = new Perms_Reflection_PermissionSet;
		$object->add( 'Registered', 'view' );
		$object->add( 'Registered', 'edit' );

		$newSet = new Perms_Reflection_PermissionSet;
		$newSet->add( 'Anonymous', 'view' );

		$target = $this->getMock( 'Perms_Reflection_Container' );
		$target->expects( $this->at(0) )
			->method( 'getDirectPermissions' )
			->will( $this->returnValue( $object ) );
		$target->expects( $this->at(1) )
			->method( 'getParentPermissions' )
			->will( $this->returnValue( $global ) );
		$target->expects( $this->at(2) )
			->method( 'remove' )
			->with( $this->equalTo( 'Registered' ), $this->equalTo( 'view' ) );
		$target->expects( $this->at(3) )
			->method( 'remove' )
			->with( $this->equalTo( 'Registered' ), $this->equalTo( 'edit' ) );

		$applier = new Perms_Applier;
		$applier->addObject( $target );
		$applier->apply( $newSet );
	}

	function testParentNotAvailable() {
		$global = new Perms_Reflection_PermissionSet;
		$global->add( 'Anonymous', 'view' );

		$newSet = new Perms_Reflection_PermissionSet;
		$newSet->add( 'Anonymous', 'view' );
		$newSet->add( 'Registered', 'edit' );

		$target = $this->getMock( 'Perms_Reflection_Container' );
		$target->expects( $this->at(0) )
			->method( 'getDirectPermissions' )
			->will( $this->returnValue( $global ) );
		$target->expects( $this->at(1) )
			->method( 'getParentPermissions' )
			->will( $this->returnValue( null ) );
		$target->expects( $this->at(2) )
			->method( 'add' )
			->with( $this->equalTo('Registered'), $this->equalTo('edit') );

		$applier = new Perms_Applier;
		$applier->addObject( $target );
		$applier->apply( $newSet );
	}

	function testMultipleTargets() {
		$global = new Perms_Reflection_PermissionSet;
		$global->add( 'Anonymous', 'view' );

		$newSet = new Perms_Reflection_PermissionSet;
		$newSet->add( 'Anonymous', 'view' );
		$newSet->add( 'Registered', 'edit' );

		$target1 = $this->getMock( 'Perms_Reflection_Container' );
		$target1->expects( $this->at(0) )
			->method( 'getDirectPermissions' )
			->will( $this->returnValue( $global ) );
		$target1->expects( $this->at(1) )
			->method( 'getParentPermissions' )
			->will( $this->returnValue( null ) );
		$target1->expects( $this->at(2) )
			->method( 'add' )
			->with( $this->equalTo('Registered'), $this->equalTo('edit') );

		$target2 = $this->getMock( 'Perms_Reflection_Container' );
		$target2->expects( $this->at(0) )
			->method( 'getDirectPermissions' )
			->will( $this->returnValue( new Perms_Reflection_PermissionSet ) );
		$target2->expects( $this->at(1) )
			->method( 'getParentPermissions' )
			->will( $this->returnValue( null ) );
		$target2->expects( $this->at(2) )
			->method( 'add' )
			->with( $this->equalTo('Anonymous'), $this->equalTo('view') );
		$target2->expects( $this->at(3) )
			->method( 'add' )
			->with( $this->equalTo('Registered'), $this->equalTo('edit') );

		$applier = new Perms_Applier;
		$applier->addObject( $target1 );
		$applier->addObject( $target2 );
		$applier->apply( $newSet );
	}

	function testRestrictChangedPermissions() {
		$before = new Perms_Reflection_PermissionSet;
		$before->add( 'Admin', 'admin' );
		$before->add( 'Registered', 'edit' );
		$before->add( 'Registered', 'view' );

		$target = $this->getMock( 'Perms_Reflection_Container' );
		$target->expects( $this->once() )
			->method( 'getDirectPermissions' )
			->will( $this->returnValue( $before ) );
		$target->expects( $this->once() )
			->method( 'getParentPermissions' )
			->will( $this->returnValue( new Perms_Reflection_PermissionSet ) );
		$target->expects( $this->once() )
			->method( 'add' )
			->with( $this->equalTo('Registered'), $this->equalTo('view_history') );

		$newSet = new Perms_Reflection_PermissionSet;
		$newSet->add( 'Registered', 'edit' );
		$newSet->add( 'Registered', 'view' );
		$newSet->add( 'Registered', 'view_history' );
		$newSet->add( 'Registered', 'admin' );

		$applier = new Perms_Applier;
		$applier->addObject( $target );
		$applier->restrictPermissions( array( 'view', 'view_history', 'edit' ) );
		$applier->apply( $newSet );
	}

	function testNoRevertToParentWithRestrictions() {
		$current = new Perms_Reflection_PermissionSet;
		$current->add( 'Anonymous', 'view' );

		$parent = new Perms_Reflection_PermissionSet;
		$parent->add( 'Anonymous', 'view' );
		$parent->add( 'Registered', 'edit' );
		$parent->add( 'Admins', 'admin' );

		$newSet = new Perms_Reflection_PermissionSet;
		$newSet->add( 'Anonymous', 'view' );
		$newSet->add( 'Registered', 'edit' );
		$newSet->add( 'Admins', 'admin' );

		$target = $this->getMock( 'Perms_Reflection_Container' );
		$target->expects( $this->once() )
			->method( 'getDirectPermissions' )
			->will( $this->returnValue( $current ) );
		$target->expects( $this->once() )
			->method( 'getParentPermissions' )
			->will( $this->returnValue( $parent ) );
		$target->expects( $this->once() )
			->method( 'add' )
			->with( $this->equalTo( 'Registered' ), $this->equalTo( 'edit' ) );

		$applier = new Perms_Applier;
		$applier->addObject( $target );
		$applier->restrictPermissions( array( 'view', 'edit' ) );
		$applier->apply( $newSet );
	}

	function testRevertIfWithinBounds() {
		$current = new Perms_Reflection_PermissionSet;
		$current->add( 'Anonymous', 'view' );

		$parent = new Perms_Reflection_PermissionSet;
		$parent->add( 'Anonymous', 'view' );
		$parent->add( 'Registered', 'edit' );
		$parent->add( 'Admins', 'admin' );

		$newSet = new Perms_Reflection_PermissionSet;
		$newSet->add( 'Anonymous', 'view' );
		$newSet->add( 'Registered', 'edit' );
		$newSet->add( 'Admins', 'admin' );

		$target = $this->getMock( 'Perms_Reflection_Container' );
		$target->expects( $this->once() )
			->method( 'getDirectPermissions' )
			->will( $this->returnValue( $current ) );
		$target->expects( $this->once() )
			->method( 'getParentPermissions' )
			->will( $this->returnValue( $parent ) );
		$target->expects( $this->once() )
			->method( 'remove' )
			->with( $this->equalTo( 'Anonymous' ), $this->equalTo( 'view' ) );

		$applier = new Perms_Applier;
		$applier->addObject( $target );
		$applier->restrictPermissions( array( 'view', 'edit', 'admin' ) );
		$applier->apply( $newSet );
	}
}
