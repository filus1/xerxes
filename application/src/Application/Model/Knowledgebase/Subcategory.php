<?php

/*
 * This file is part of Xerxes.
 *
 * (c) California State University <library@calstate.edu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\Model\Knowledgebase;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Subcategory
 *
 * @author David Walker <dwalker@calstate.edu>
 * 
 * @Entity @Table(name="subcategories")
 */

class Subcategory
{
	/** @Id @Column(type="integer") @GeneratedValue **/
	protected $id;
	
	/**
	 * @Column(type="string")
	 * @var string
	 */
	protected $owner;

	/**
	 * @Column(type="string", nullable=true)
	 * @var string
	 */
	protected $source_id;
	
	/**
	 * @Column(type="string")
	 * @var string
	 */
	protected $name;
	
	/**
	 * @Column(type="integer", nullable=true)
	 * @var int
	 */
	protected $sequence = 999;
	
	/**
	 * @Column(type="boolean", nullable=true)
	 * @var boolean
	 */	
	protected $sidebar = false;
	
	/**
	 * @ManyToOne(targetEntity="Category", inversedBy="subcategories")
	 * @JoinColumn(name="category_id", referencedColumnName="id", nullable=false)
	 * @var Category
	 */
	protected $category;
	
	/**
	 * @OneToMany(targetEntity="DatabaseSequence", mappedBy="subcategory", cascade={"persist"})
	 * @OrderBy({"sequence" = "ASC"})
	 * @var DatabaseSequence[]
	 */	
	protected $database_sequences;
	
	/**
	 * Create new Subcategory
	 */
	
	public function __construct()
	{
		$this->database_sequences = new ArrayCollection();
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}
	
	/**
	 * @return string
	 */
	public function getOwner()
	{
		return $this->owner;
	}
	
	/**
	 * @param string $source_id
	 */
	public function setOwner($owner)
	{
		$this->owner = $owner;
	}	
	
	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * @return int
	 */
	public function getSequence()
	{
		return $this->sequence;
	}

	/**
	 * @param int $sequence
	 */
	public function setSequence($sequence)
	{
		$this->sequence = $sequence;
	}
	
	/**
	 * @param bool $bool
	 */
	public function setSidebar($bool)
	{
		$this->sidebar = (bool) $bool;
	}
	
	/**
	 * return bool
	 */
	public function getSidebar()
	{
		return $this->sidebar;
	}

	/**
	 * @param string $id
	 */
	public function setSourceId($id)
	{
		$this->source_id = $id;
	}
	
	/**
	 * return string
	 */
	public function getSourceId()
	{
		return $this->source_id;
	}	
	
	/**
	 * @param Category $category
	 */
	public function setCategory(Category $category) 
	{
		$this->category = $category;
	}

	/**
	 * @return array DatabaseSequence
	 */
	public function getDatabases()
	{
		return $this->database_sequences->toArray();
	}
	
	/**
	 * @param DatabaseSequence $sequence
	 */
	public function addDatabaseSequence(DatabaseSequence $sequence)
	{
		// check to make sure the database isn't already assigned
		
		foreach ( $this->database_sequences as $database_sequence )
		{
			if ( $database_sequence->getDatabase()->getId() == $sequence->getDatabase()->getId() )
			{
				return null; // don't add it
			}
		}
		
		$sequence->setSubcategory($this);
		$sequence->setOwner($this->owner);
		
		$this->database_sequences[] = $sequence;
	}
	
	
	/**
	 * @return array
	 */
	
	public function toArray()
	{
		$final = array();
	
		foreach ( $this as $key => $value )
		{
			if ( $key == 'category')
			{
				continue;
			}
			
			if ( $key == 'database_sequences')
			{
				$final[$key] = $value->toArray();
			}
			else
			{
				$final[$key] = $value;
			}
		}
	
		return $final;
	}
}