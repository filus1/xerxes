<?php

/*
 * This file is part of Xerxes.
 *
 * (c) California State University <library@calstate.edu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\Model\DataMap;

use Xerxes\Utility\User;
use Xerxes\Utility\DataMap;

/**
 * Database access mapper for users
 *
 * @author David Walker <dwalker@calstate.edu>
 */

class Users extends DataMap
{
	/**
	 * Update the user table to include the last date of login and any other
	 * specified attributes. Creates new user if neccesary.
	 * If any attributes in User are set other than
	 * username, those will also be written to db over-riding anything that may
	 * have been there.  Returns User filled out with information matching
	 * db. 
	 *
	 * @param User $user
	 * @return User $user
	 */
	
	public function touchUser(User $user)
	{
		// array to pass to db updating routines. Make an array out of our
		// properties. 

		$update_values = array();
		
		foreach ( $user->properties() as $key => $value )
		{
			if ( $value != '' )
			{
				$update_values[":" . $key] = $value;
			}
		}
		
		// don't use usergroups though. 
		
		unset( $update_values[":usergroups"] );
		
		$update_values[":last_login"] = date( "Y-m-d H:i:s" );
		
		$this->beginTransaction();
		
		$strSQL = "SELECT * FROM xerxes_users WHERE username = :username";
		
		$arrResults = $this->select( $strSQL, array (":username" => $user->username ) );
		
		if ( count( $arrResults ) == 1 )
		{
			// user already exists in database, so update the last_login time and
			// use any data specified in our User record to overwrite. Start
			// with what's already there, overwrite with anything provided in
			// the User object. 
			
			$db_values = $arrResults[0];
			
			foreach ( $db_values as $key => $value )
			{
				if ( ! (is_null( $value ) || is_numeric( $key )) )
				{
					$dbKey = ":" . $key;
					
					// merge with currently specified values

					if ( ! array_key_exists( $dbKey, $update_values ) )
					{
						$update_values[$dbKey] = $value;
						$user->$key = $value; // update user
					}
				}
			}
			
			$strSQL = "UPDATE xerxes_users SET";
			
			foreach ( array_keys($update_values) as $key )
			{
				$strSQL .= ' ' . str_replace(':', '', $key) . '=' . $key . ',';
			}
			
			$strSQL = substr($strSQL, 0, -1); // get last comma
			
			$strSQL .= " WHERE username = :username";
			
			$status = $this->update( $strSQL, $update_values );
		} 
		else
		{
			// add 'em otherwise
			
			$keys = array();
			
			foreach ( array_keys($update_values) as $key )
			{
				$keys[] = str_replace(':', '', $key);
			}

			$strSQL = 'INSERT INTO xerxes_users (' . implode(',', $keys) . ')';
			$strSQL .= ' VALUES (' . implode(',', array_keys($update_values)) . ')';
			
			$status = $this->insert( $strSQL, $update_values );
		}
		
		// let's make our group assignments match, unless the group
		// assignments have been marked null which means to keep any existing ones
		// only.

		if ( is_null( $user->usergroups ) )
		{
			// fetch what's in the db and use that please.

			$fetched = $this->select( 
				"SELECT usergroup FROM xerxes_user_usergroups WHERE username = :username", 
				array (":username" => $user->username ) 
			);
			
			if ( count( $fetched ) )
			{
				$user->usergroups = $fetched[0];
			}
			else
			{
				$user->usergroups = array();
			}
		} 
		else
		{
			$status = $this->delete( 
				"DELETE FROM xerxes_user_usergroups WHERE username = :username", 
				array (":username" => $user->username ) 
			);
			
			foreach ( $user->usergroups as $usergroup )
			{
				$status = $this->insert( "INSERT INTO xerxes_user_usergroups (username, usergroup) " .
					"VALUES (:username, :usergroup)", 
					array (":username" => $user->username, ":usergroup" => $usergroup ) 
				);
			}
		}
		
		$this->commit();
		
		return $user;
	}
	
	/**
	 * Associate a user with the supplied lti user id
	 * 
	 * @param string $user     xerxes user
	 * @param string $user_id  lti user id
	 */
	
	public function associateUserWithLti($username, $user_id)
	{
		if (trim($username) == "")
		{
			throw new \DomainException("Username cannot be null");
		}
		
		$sql = 'INSERT INTO xerxes_reading_list_users (id, username) VALUES (:id, :username)';
		$params = array (":id" => $user_id, ":username" => $username );
		
		$status = $this->insert( $sql, $params);
	}
	
	/**
	 * Get the User from the supplied lti user id
	 * 
	 * @param string $user_id  lti user id
	 * @return User
	 */
	
	public function getUserFromLti($user_id)
	{
		$sql = 'SELECT username FROM xerxes_reading_list_users WHERE id = :id';
		$params = array (':id' => $user_id);
		
		$results = $this->select($sql, $params);
		
		if ( count($results) == 0 )
		{
			return null;
		}
		else
		{
			return $results[0]['username'];
		}
	}
}
