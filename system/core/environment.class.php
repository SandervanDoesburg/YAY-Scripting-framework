<?php
/**
 * @author YAY!Scripting
 * @package files
 */
namespace System;

/** Environment
 * 
 * This class determines in what environment you are, and if you are logged into that environment.
 *
 * @name Environment
 * @package core
 * @subpackage Router
 */
class Environment extends Singleton
{
	
	/** The route to use while determining the environment
	 * 
	 * @access private
	 * @var array
	 */
	private $route = null;
	
	/** All config-data
	 * 
	 * @access public
	 * @var array $config
	 */
	private $config;
	
	/** Checked environment
	 * 
	 * @access private
	 * @var string $cache
	 */
	private $cache = null;
	
	/** Constructor
	 * 
	 * Saves config.
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		
		// load config
		$this->config = Config::Load();
		
		// checks singleton
		parent::__construct();
		
	}
	
	/** Sets the route
	 * 
	 * @access public
	 * @param array $route The route
	 * @return void
	 */
	public function setRoute(array $route)
	{
		
		$this->route = $route;
		
	}
	
	/** Captures GET
	 * 
	 * Gives back values about the environment
	 * Selects from the current environment
	 * 
	 * @access public
	 * @param string $name Key to search for
	 * @return string Value
	 */
 	public function __get($name) {
 		
 		// get name
 		if (is_null($this->cache))
 			$this->get();
 			
 		// lower name
 		$name = strtolower($name);
 		
 		// check value
 		switch ($name) {
 			
 			case 'enabled':
 			case 'trigger':
 			case 'folder':
 			case 'default_controller':
 			case 'login':
 			case 'login_controller':
 			
 				if (empty($this->config->environment->{$this->cache}) || empty ($this->config->environment->{$this->cache}->$name))
 					return null;
 			
 				$a = $this->config->environment->{$this->cache}->$name;
 			
 				if (!empty($a)){
 				
 					return $a;
 					
 				}
				
		}
		
		return false;
 		
	}
	
	/** Checks if you are in an environment
	 * 
	 * @access public
	 * @return string Name of environment
	 */
	public function get()
	{
		
		if (!is_null($this->cache))
			return $this->cache;
		
		
		foreach ($this->config->environment as $name => $values) {
			
			if ($values->enabled == true && (isset($this->route[0]) && strtolower($this->route[0]) == strtolower($values->trigger))) {
				
				$this->cache = $name;
				return $name;
				
			}
			
			
		}
		
		return false;
		
	}
	
	/** Logs into an environment
	 * 
	 * Default environment is the current one
	 * 
	 * @access public
	 * @param string $env Environment to log in to.
	 * @return bool success
	 */
 	public function login($env = null)
 	{
 		
 		// get current environment
 		if (is_null($env)) {
 			
 			if (is_null($this->cache))
 				$this->get();
 				
			$env = $this->cache;
 			
		}
		
		// login
		$_SESSION['environment'][$env] = $_SERVER['REMOTE_ADDR'];
 		
		 return true;
 		
	}
	
	/** Logs out of an environment
	 * 
	 * Default environment is the current one
	 * 
	 * @access public
	 * @param string $env Environment to out of.
	 * @return void
	 */
 	public function logout($env = null)
 	{
 		
 		// get current environment
 		if (is_null($env)) {
 			
 			if (is_null($this->cache))
 				$this->get();
 				
			$env = $this->cache;
 			
		}
		
		// login
		unset($_SESSION['environment'][$env]);
 		
	}
	
	/** Checks if you are logged into a specific environment
	 * 
	 * Default environment is the current one
	 * 
	 * @access public
	 * @param string $env Environment to check.
	 * @return bool logged in
	 */
 	public function loggedin($env = null)
 	{
 		
 		// get current environment
 		if (is_null($env)) {
 			
 			if (is_null($this->cache))
 				$this->get();
 				
			$env = $this->cache;
 			
		}
		
		// no login nessecary
		if ($this->__get('login') === false)
			return true;
		
		// warningless check
		if (empty($_SESSION['environment'][$env]))
			return false;
			
		if ($_SESSION['environment'][$env] != $_SERVER['REMOTE_ADDR']) {
			
			unset($_SESSION['environment']);
			session_regenerate_id(true);
			setcookie("ysf_session", "", time() - 3600);
			setcookie('ysf_session', session_id(), time() + 126147624/*+4years*/); 
			
		}
		
		// check
		return ($_SESSION['environment'][$env] == $_SERVER['REMOTE_ADDR']);
 		
	}
	
}