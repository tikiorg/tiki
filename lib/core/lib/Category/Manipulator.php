<?php

class Category_Manipulator
{
	private $objectType;
	private $objectId;

	private $current = array();
	private $managed = array();
	private $new = array();

	private $prepared = false;

	function __construct( $objectType, $objectId ) {
		$this->objectType = $objectType;
		$this->objectId = $objectId;
	}

	function setCurrentCategories( array $categories ) {
		$this->current = $categories;
	}

	function setManagedCategories( array $categories ) {
		$this->managed = $categories;
	}

	function setNewCategories( array $categories ) {
		$this->new = $categories;
	}

	function getAddedCategories() {
		if( ! $this->canModifyObject() ) {
			return array();
		}

		$this->prepare();

		$attempt = array_diff( $this->new, $this->current );
		return $this->filter( $attempt, 'add_object' );
	}

	function getRemovedCategories() {
		if( ! $this->canModifyObject() ) {
			return array();
		}

		$this->prepare();

		$attempt = array_diff( $this->current, $this->new );
		return $this->filter( $attempt, 'remove_object' );
	}

	private function filter( $categories, $permission ) {
		$out = array();
		foreach( $categories as $categ ) {
			$perms = Perms::get( array( 'type' => 'category', 'object' => $categ ) );

			if( $perms->$permission ) {
				$out[] = $categ;
			}
		}

		return $out;
	}

	private function canModifyObject() {
		$objectperms = Perms::get( array( 'type' => $this->objectType, 'object' => $this->objectId ) );

		return $objectperms->modify_object_categories;
	}

	private function prepare() {
		if( $this->prepared ) {
			return;
		}

		$categories = $this->managed;
		Perms::bulk( array( 'type' => 'category' ), 'object', $categories );

		if( $this->managed ) {
			$this->current = array_intersect( $this->current, $this->managed );
			$this->new = array_intersect( $this->new, $this->new );
		}

		$this->prepared = true;
	}
}
