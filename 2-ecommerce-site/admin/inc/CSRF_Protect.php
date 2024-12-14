<?php

class CSRF_Protect
{
	
	private $namespace;
	
	public function __construct($namespace = '_csrf')
	{
		$this->namespace = $namespace;
		
		if (session_id() === '')
		{
			session_start();
		}
		
		$this->setToken();
	}
	
	public function getToken()
	{
		return $this->readTokenFromStorage();
	}
	
	public function isTokenValid($userToken)
	{
		return ($userToken === $this->readTokenFromStorage());
	}
	
	/**
	 * Echoes the HTML input field with the token, and namespace as the
	 * name of the field
	 */
	public function echoInputField()
	{
		$token = $this->getToken();
		echo "<input type=\"hidden\" name=\"{$this->namespace}\" value=\"{$token}\" />";
	}
	
	/**
	 * Verifies whether the post token was set, else dies with error
	 */
	public function verifyRequest()
	{
		if (!$this->isTokenValid($_POST[$this->namespace]))
		{
			die("CSRF validation failed.");
		}
	}
	
	/**
	 * Generates a new token value and stores it in persisent storage, or else
	 * does nothing if one already exists in persisent storage
	 */
	private function setToken()
	{
		$storedToken = $this->readTokenFromStorage();
		
		if ($storedToken === '')
		{
			$token = md5(uniqid(rand(), TRUE));
			$this->writeTokenToStorage($token);
		}
	}
	
	/**
	 * Reads token from persistent sotrage
	 * @return string
	 */
	private function readTokenFromStorage()
	{
		if (isset($_SESSION[$this->namespace]))
		{
			return $_SESSION[$this->namespace];
		}
		else
		{
			return '';
		}
	}
	
	/**
	 * Writes token to persistent storage
	 */
	private function writeTokenToStorage($token)
	{
		$_SESSION[$this->namespace] = $token;
	}
}
