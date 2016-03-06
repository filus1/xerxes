<?php

/*
 * This file is part of Xerxes.
 *
 * (c) California State University <library@calstate.edu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\Model\Primo;

use Application\Model\Search;
use Application\Model\Search\Query\Url;
use Xerxes\Mvc\Request;

/**
 * Solr Search Query
 *
 * @author David Walker <dwalker@calstate.edu>
 */

class Query extends Search\Query
{
	/**
	 * primo server address
	 * @var string
	 */
	protected $server;
	
	/**
	 * primo institution id
	 * @var string
	 */
	protected $institution;
	
	/**
	 * not sure what this is, 'vendor' id?
	 * @var string
	 */
	protected $vid;
	
	/**
	 * scope value(s)
	 * @var array
	 */
	protected $loc = array();

	/**
	 * on campus or not
	 * @var bool
	 */
	protected $on_campus = true;
	
	/**
	 * Create a Primo Query
	 *
	 * @param Request $request
	 * @param Config $config
	 */
	
	public function __construct(Request $request = null, Config $config = null )
	{
		parent::__construct($request, $config);
		
		// server info
		
		$this->server = $this->config->getConfig('PRIMO_ADDRESS', true);
		$this->server = rtrim($this->server, '/');
		
		// institutional id's
		
		$this->institution = $this->config->getConfig('INSTITUTION', true);
		$this->vid = $this->config->getConfig('VID', false);
		
		// scope
		
		$loc = $this->config->getConfig('LOC', false, $this->request->getParam('scope'));
		
		if ( $loc != "" )
		{
			$this->loc = explode(";", $loc);
		}
	}
	
	/**
	 * Whether user is on-campus or not
	 * 
	 * @param unknown_type $bool
	 */
	
	public function setOnCampus($bool)
	{
		$this->on_campus = $bool;
	}
	
	/**
	 * Convert to Primo individual record syntax
	 *
	 * @param string $id
	 * @return Url
	 */
	
	public function getRecordUrl($id)
	{
		$id = urlencode($id);
		
		$url = $this->server . '/xservice/search/brief?' .
			"&query=rid,exact,$id" .
			'&indx=1&bulkSize=1';
		
		$url = $this->addLocationParams($url);
			
		return new Url($url);
	}
	
	/**
	 * Convert to Primo query syntax
	 * 
	 * @return Url
	 */
	
	public function getQueryUrl()
	{
		$query = ""; // query
		
		foreach ( $this->getQueryTerms() as $term )
		{
			$query .= "&query=" . $term->field_internal . ",contains," . urlencode($term->phrase);
		}
			
		foreach ( $this->getLimits() as $limit )
		{
			$value = $limit->value;
			
			if ( is_array($limit->value) )
			{
				$value = implode(',', $limit->value);
			}
			
			if ( $limit->field == "tlevel")
			{
				if ($value == "Online Resources")
				{
					$query .= "&query_inc=facet_tlevel,exact,online_resources_PC_TN";
				}
				elseif ($value == "Peer Reviewed")
				{
					$query .= "&query_inc=facet_tlevel,exact,peer_reviewed";
				}
			}
			else
			{
				$type = 'query_inc';
				
				if ( $limit->boolean == "NOT" )
				{
					$type = 'query_exc';
				}
				
				$query .= "&$type=facet_" . $limit->field . ",exact," . urlencode($value);
			}
		}
		
		// on campus as string
		
		$on_campus = "true";
		
		if ( $this->on_campus == false )
		{
			$on_campus = "false";
		}
		
		// create the url
		
		$url = $this->server . '/xservice/search/brief?' .
			$query .
			'&indx=' . $this->start .
			'&bulkSize=' . $this->max;

		
		$url = $this->addLocationParams($url);
			
		if ( $this->sort != "" )
		{
			$url .= '&sortField=' . $this->sort;
		}
		
		// echo $url;
		
		return new Url($url);
	}
	
	protected function addLocationParams($url)
	{
		$url .= '&institution=' . $this->institution;
		
		$on_campus = "true";
		
		if ( $this->on_campus == false )
		{
			$on_campus = "false";
		}
		
		$url .= '&onCampus=' . $this->on_campus;
		
		if ( $this->vid != "" )
		{
			$url .= "&vid=" . $this->vid;
		}
		
		foreach ( $this->loc as $loc )
		{
			$url .= "&loc=" . $loc;
		}
		
		return $url;
	}
}
