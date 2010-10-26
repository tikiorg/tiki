<?php

interface Search_ContentSource_Interface
{
	/**
	 * Provides a list of type-specific object IDs available in the database.
	 *
	 * @return Traversable
	 */
	function getDocuments();

	/**
	 * Provides teh basic data for the specified object ID.
	 *
	 * @return array
	 */
	function getDocument($objectId, Search_Type_Factory_Interface $typeFactory);
}
