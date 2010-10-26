<?php

class Search_Index_Lucene implements Search_Index_Interface
{
	private $lucene;

	function __construct($directory)
	{
		try {
			$this->lucene = Zend_Search_Lucene::open($directory);
		} catch (Zend_Search_Lucene_Exception $e) {
			$this->lucene = Zend_Search_Lucene::create($directory);
		}
	}

	function addDocument(array $data)
	{
		$document = $this->generateDocument($data);

		$this->lucene->addDocument($document);
	}

	function rawQuery($query)
	{
		$hits = $this->lucene->find($query);
		$result = array();

		foreach ($hits as $hit) {
			$result[] = array(
				'object_type' => $hit->object_type,
				'object_id' => $hit->object_id,
			);
		}

		return $result;
	}

	function getTypeFactory()
	{
		return new Search_Type_Factory_Lucene;
	}

	private function generateDocument($data)
	{
		$document = new Zend_Search_Lucene_Document;
		$typeMap = array(
			'Search_Type_WikiText' => 'Text',
			'Search_Type_Whole' => 'Keyword',
		);

		foreach ($data as $key => $value) {
			$luceneType = $typeMap[get_class($value)];
			$document->addField(Zend_Search_Lucene_Field::$luceneType($key, $value->getValue()));
		}

		return $document;
	}
}

