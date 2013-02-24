<?php
namespace DavideDebernardi\Happening;

interface EventListenerInterface
{
	public function listenTo(EventInterface $event);
}
