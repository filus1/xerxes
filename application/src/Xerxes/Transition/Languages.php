<?php

/**
 * Parses and holds information about language codes and names
 *
 * @author Ivan Masar
 * @copyright 2010 Ivan Masar
 * @link http://xerxes.calstate.edu
 * @license http://www.gnu.org/licenses/
 * @version $Id: Languages.php 1778 2011-03-07 16:34:04Z dwalker@calstate.edu $
 * @package  Xerxes_Framework
 */

class Xerxes_Framework_Languages
{
	protected $xpath = "";		// language data we can query
	protected $gettext = false; // whether gettext is installed
	protected $languages_file_system = "/usr/share/xml/iso-codes/iso_639.xml";
	protected $languages_file_xerxes = "data/iso_639.xml"; // local version
	protected $gettext_domain_system = "/usr/share/locale";
	protected $gettext_domain_xerxes = "data/locale"; // local version
	protected $locale = "C";	// default locale
	protected $domain = "iso_639";	// gettext domain
	private static $instance;	// singleton pattern
	
	protected function __construct()
	{
	}
	
	/**
	 * Get an instance of the file; Singleton to ensure correct data
	 *
	 * @return Xerxes_Framework_Languages
	 */
	
	public static function getInstance()
	{
		if ( empty( self::$instance ) )
		{
			self::$instance = new Xerxes_Framework_Languages( );
			self::$instance->init();
		}
		
		return self::$instance;
	}
	
	/**
	 * Initialize the object by picking up and processing the ISO 639 xml file
	 * 
	 * @exception 	will throw exception if no file can be found
	 */
	
	public function init()
	{
		// first, see if Getttext functions are installed
		
		if ( function_exists( 'bindtextdomain' ) )
		{
			$this->gettext = true;
		}
			
		// set full path to local copy
		
		$this->languages_file_xerxes = realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->languages_file_xerxes);
		$this->gettext_domain_xerxes = realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->gettext_domain_xerxes);
		
		// if the iso-codes is not installed, use our copy
		
		$file = "";
		
		if ( file_exists( $this->languages_file_system ) )
		{
			$file = $this->languages_file_system;
		}
		elseif ( file_exists( $this->languages_file_xerxes) )
		{
			$file = $this->languages_file_xerxes;
		}
		else
		{
			throw new Exception( "could not find file with the ISO 639 language list" );
		}
		
		// load the languages file
		
		$xml = new DOMDocument();
		$xml->load( $file );
		
		$this->xpath = new DOMXPath( $xml );
		
		unset($xml);
		
		// which language shall we display?
		
		$objRegistry = Xerxes_Framework_Registry::getInstance();
		$objRequest = Xerxes_Framework_Request::getInstance();
		$lang = $objRequest->getProperty("lang");
		
		if ( $lang == null ) {
			$lang = $objRegistry->defaultLanguage();
		}
		
		$this->locale = $objRegistry->getLocale($lang);
		
		// bindings
		
		if ( $this->gettext == true )
		{
			$gettext_domain_path = "";
			
			if ( file_exists( $this->gettext_domain_xerxes) )
			{
				$gettext_domain_path = $this->gettext_domain_xerxes;
			}
			elseif ( file_exists( $this->gettext_domain_system ) )
			{
				$gettext_domain_path = $this->gettext_domain_system;
			}
			
			bindtextdomain( $this->domain, $gettext_domain_path ); // this works on windows too?
			bind_textdomain_codeset( $this->domain, 'UTF-8' );	// assume UTF-8, all the .po files in iso_639 use it
			textdomain( $this->domain );
		}
	}
	
	/**
	 * Get localized language name of provided ISO 639 code
	 *
	 * @param string $type			the standard according to which the code will be interpreted;
	 * 					one of: iso_639_1_code, iso_639_2B_code
	 * @param string $code			the 2-letter language code
	 * @param string $override_locale	use this locale instead of Xerxes locale
	 * @return mixed			A string with the localized language name or NULL if the code is not valid
	 */
	
	public function getNameFromCode( $type, $code, $override_locale = null )
	{
		if ($type != 'name')
		{
			$code = Xerxes_Framework_Parser::strtolower( $code );
		}
		
		$elements = $this->xpath->query( "//iso_639_entry[@$type='$code']" ); 
		
		if ( ! is_null( $elements ) )
		{
			foreach ($elements as $element)
			{
				$name = $element->getAttribute( 'name' );
				
				if ( $this->gettext == false )
				{
					return $name;
				}
				
				$originalLocale = $this->getXerxesLocale();
				
				if  ( $override_locale == null) {
					$this->setXerxesLocale( $this->locale );
				} else {
					$this->setXerxesLocale( $override_locale );
				}
				
				$languageName = dgettext( $this->domain, $name );
				
				$this->setXerxesLocale( $originalLocale );
				
				return $languageName;
			}
		}
		else
		{
			return null;
		}
	}
	
	public function getXML()
	{
		return $this->xml;
	}
	
	private function getXerxesLocale()
	{
		if ( defined('LC_MESSAGES') )
		{
			return setlocale( LC_MESSAGES, null );
		}
	}
	
	private function setXerxesLocale( $locale )
	{
		$result = false;
		
		if ( defined('LC_MESSAGES') )
		{
			$result = setlocale( LC_MESSAGES, $locale );
		}
		
		return $result;
	}
}
?>
