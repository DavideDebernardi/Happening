<?php
namespace DavideDebernardi\Happening;

interface EventBrokerInterface
{
	/**
	 * Dispatches an event:
	 * - calls to this method will notify all listeners, unless one of them is flagging the stop propagation of the 
	 * event itself; 
	 * - calls to this method passing an emitter that is not currently registered will result in an RuntimeException; 
	 * - if the implementation of EventInterface is not returning a string value for EventInterface::getTopic, an 
	 * UnexpectedValueException is thrown.
	 * 
	 * @param EventEmitterInterface $emitter
	 * @param EventInterface $event
	 * @return EventBrokerInterface
	 * @throws \RuntimeException, \UnexpectedValueException
	 */
	public function dispatchEvent(EventEmitterInterface $emitter, EventInterface $event);
	
	/**
	 * Registers an emitter and injects the EventBroker into it.
	 *
	 * @param EventEmitterInterface $emitter
	 * @return EventBrokerInterface
	 */
	public function registerEmitter(EventEmitterInterface $emitter);
	
	/**
	 * Registers a listener for a given topic string:
	 * - should always throw an \UnexpectedValueException if the name is not valid;
	 * - fluent interface should be provided;
	 * 
	 * @param EventListenerInterface $listener
	 * @param mixed $topic
	 * @return EventBrokerInterface
	 * @throws \UnexpectedValueException
	 */
	public function registerListener(EventListenerInterface $listener, $topic);
	
	/**
	 * 
	 * Registers a listener for a given topic string:
	 * - should always throw an \UnexpectedValueException if the name is not valid;
	 * - fluent interface should be provided;
	 * 
	 * @param EventListenerInterface $listener
	 * @param mixed $topic
	 * @return EventBrokerInterface
	 * @throws \UnexpectedValueException
	 */
	public function unregisterListener(EventListenerInterface $listener, $topic);
	
	/**
	 * Unregisters an EventEmitterInterface instace:
	 * - fluent interface should be enforced;
	 * 
	 * @param EventEmitterInterface $emitter
	 * @return EventBrokerInterface
	 */
	public function unregisterEmitter(EventEmitterInterface $emitter);
	
	/**
	 * Checks if the EventBroker has a given topic registered with some listeners.
	 * 
	 * @param mixed $topic
	 * @return bool
	 */
	public function hasTopic($topic);
}
