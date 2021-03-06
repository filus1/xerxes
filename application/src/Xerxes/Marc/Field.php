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

/**
 *  Abstract field object
 * 
 * @author David Walker <dwalker@calstate.edu>
 */

abstract class Field
{
	protected $value;
	
	public function __toString()
	{
		return (string) $this->value;
	}
}