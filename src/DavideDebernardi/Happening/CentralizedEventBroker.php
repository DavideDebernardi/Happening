<?php
namespace DavideDebernardi\Happening;

class CentralizedEventBroker extends EventBrokerAbstract
{
	private static $_instance;
	
	private function __construct()
	{
		
	}
	
	public static function getInstance()
	{
		if(is_null(self::$_instance)){
			self::$_instance = new self();
		}
		return self::$_instance;
	}
}