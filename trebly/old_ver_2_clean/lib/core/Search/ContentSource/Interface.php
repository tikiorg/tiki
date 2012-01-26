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

	/**
	 * Returns an array containing the list of field names that can be provided
	 * by the content source.
	 */
	function getProvidedFields();

	/**
	 * Returns an array containing the list of field names that must be included
	 * in the global content.
	 */
	function getGlobalFields();
}
