<?php
namespace DavideDebernardi\Happening;

interface EventEmitterInterface
{
	public function setEventBroker(EventBrokerInterface $broker = null);
}
