<?php
namespace DavideDebernardi\Happening;

interface EventInterface
{	
	public function __construct($topic, array $data = array());
	
	public function getTopic();
	
	public function getData();
	
	public function setStopPropagation($boolean);
	
	public function getStopPropagation();
}
