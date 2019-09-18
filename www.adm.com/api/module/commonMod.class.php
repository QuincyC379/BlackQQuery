<?php
class commonMod{
	protected $config = array();
	protected $user = array();
	private $_data = array();

	public function __construct()
	{
		global $config;
		$this->config = $config;
	}

	public function __get($name)
	{
		return isset( $this->_data[$name] ) ? $this->_data[$name] : NULL;
	}

	public function __set($name, $value)
	{
		$this->_data[$name] = $value;
	}
}