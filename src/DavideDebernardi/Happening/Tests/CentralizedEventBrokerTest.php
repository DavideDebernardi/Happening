<?php
namespace DavideDebernardi\Happening\Tests;

use DavideDebernardi\Happening\CentralizedEventBroker;

use DavideDebernardi\Happening\EventBrokerInterface,
    DavideDebernardi\Happening\EventListenerInterface,
    DavideDebernardi\Happening\EventEmitterInterface,
    DavideDebernardi\Happening\EventInterface,
    DavideDebernardi\Happening\EventBrokerAbstract;

class CentralizedEventBrokerTest extends EventBrokerAbstractTest
{	
	protected function getBrokerMock()
	{
		return CentralizedEventBroker::getInstance();
	}
}