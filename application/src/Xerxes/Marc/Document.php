<?php

/*
 * This file is part of Xerxes.
 *
 * (c) California State University <library@calstate.edu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Xerxes\Marc;

use Xerxes\Utility\Parser;

/**
 * MARC Document
 * 
 * @author David Walker <dwalker@calstate.edu>
 */

class Document
{
	protected $namespace = "http://www.loc.gov/MARC21/slim";
	protected $_length = 0;
	protected $_records = array();
	
	/**
	 * Load a MARC-XML document from string or object
	 *
	 * @param string|DOMNode|DOMDocument $xml
	 */
	
	public function loadXML($xml)
	{
		$xml = Parser::convertToDOMDocument($xml);
		$this->parse($xml);
	}
	
	/**
	 * Load a MARC-XML document from file
	 *
	 * @param string $file		location of file, can be uri
	 */
	
	public function load($file)
	{
		$document = new \DOMDocument();
		$document->load($file);
		
		$this->loadXML($document);
	}
	
	/**
	 * Parse the XML into objects
	 *
	 * @param \DOMDocument $document
	 */

	protected function parse(\DOMDocument $document)
	{
		$xpath = new \DOMXPath($document);
		$xpath->registerNamespace("marc", $this->namespace);
		
		$records = $xpath->query("//marc:record");
		$this->_length = $records->length;
		
		foreach ( $records as $record )
		{
			$marc_record = new Record;
			$marc_record->loadXML($record);
			array_push($this->_records, $marc_record);
		}
	}
	
	/**
	 * Get the record at the specific position
	 *
	 * @param int $position		[optional] position of the record (index starts at 1), default is 1
	 * @return Record
	 */
	
	public function record($position = 1)
	{
		$position--;
		return $this->_records[$position];
	}
	
	/**
	 * List of MARC Records from the Document
	 *
	 * @return Record[]
	 */
	
	public function records()
	{
		return $this->_records;
	}
	
	/**
	 * The number of MARC Records in the Document
	 *
	 * @return unknown
	 */
	
	public function length()
	{
		return $this->_length;
	}
	
	public function __get($property)
	{
		if ( method_exists($this, $property) )
		{
			return $this->$property();
		}
	}
}