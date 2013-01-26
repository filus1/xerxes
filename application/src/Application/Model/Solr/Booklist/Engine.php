<?php

/*
 * This file is part of the Xerxes project.
 *
 * (c) California State University <library@calstate.edu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\Model\Solr\Booklist;

use Application\Model\Solr;
use Xerxes;
use Xerxes\Record\Subject;
use Xerxes\Record\Author;

class Engine extends Solr\Engine 
{
	protected function extractRecords($xml)
	{
		$records = array();
		$docs = $xml->xpath("//doc");
		
		if ( $docs !== false && count($docs) > 0 )
		{
			foreach ( $docs as $doc )
			{
				$xml_data = "";
				
				foreach ( $doc->str as $str )
				{
					// record
											
					if ( (string) $str["name"] == 'fullrecord' )
					{
						$xml_data = (string) $str;
					}
				}
				
				$record = new Record();
				$record->loadXML($xml_data);
				
				array_push($records, $record);
			}
		}
		
		return $records;
	}
}


class Record extends Xerxes\Record
{
	protected function map()
	{
		$xml = simplexml_import_dom($this->document->documentElement);
		
		$this->title  = (string) $xml->title;
		$this->edition  = (string) $xml->edition;
		$this->publisher  = (string) $xml->publisher;
		$this->list_price = (string) $xml->list_price;
		$this->discount_price = (string) $xml->discount_price;
		$this->term = (string) $xml->term;
		$this->supplier = (string) $xml->supplier;
		
		$this->isbns[] = (string) $xml->isbn;
		
		$author = new Author();
		$author->name = (string) $xml->author;
		$this->authors[] = $author;
		
		$subject = new Subject();
		$subject->value = (string) $xml->subject;
		$this->subjects[] = $subject;
		
	}
}