<?php
namespace DavideDebernardi\Happening;

interface EventBrokerInterface
{
	public function dispatchEvent(EventEmitterInterface $emitter, EventInterface $event);
	
	public function registerEmitter(EventEmitterInterface $emitter);
	
	public function registerListener(EventListenerInterface $listener, $topic);
	
	public function unregisterEmitter(EventEmitterInterface $emitter);
	
	public function unregisterListener(EventListenerInterface $listener, $topic);
}
