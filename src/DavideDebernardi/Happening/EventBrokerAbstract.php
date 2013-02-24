<?php
namespace DavideDebernardi\Happening;

class EventBrokerAbstract implements EventBrokerInterface
{
	protected $emitters = array();
	
	protected $listeners = array();
	
	/**
	 * 
	 * 
	 * @param EventEmitterInterface $emitter
	 * @param EventInterface $event
	 * @return EventBrokerAbstract
	 * @throws \RuntimeException, \UnexpectedValueException
	 */
	public function dispatchEvent(EventEmitterInterface $emitter, EventInterface $event)
	{
		if(!$this->hasEmitter($emitter)){
			throw new \RuntimeException('An emitter is trying to dispatch an event but it\'s not registered.');
		}
		$topic = $event->getTopic();
		if($this->hasTopic($topic) && $this->topicHasListeners($topic)){
			foreach ($this->listeners[$topic] as $listener) {
				$listener->listenTo($event);
				if($event->getStopPropagation()){
					break;
				}
			}
		}
		return $this;
	}
	
	/**
	 * 
	 *
	 * @param EventEmitterInterface $emitter
	 * @return EventBrokerAbstract
	 */
	public function registerEmitter(EventEmitterInterface $emitter)
	{
		if(!$this->hasEmitter($emitter)){
			$this->addEmitter($emitter);
		}
		$emitter->setEventBroker($this);
		return $this;
	}
	
	/**
	 * 
	 * 
	 * @param EventEmitterInterface $emitter
	 * @return EventBrokerAbstract
	 */
	public function unregisterEmitter(EventEmitterInterface $emitter)
	{
		if($this->hasEmitter($emitter)){
			$this->removeEmitter($emitter);
			$emitter->setEventBroker(null);
		}
		return $this;
	}
	
	/**
	 * 
	 * 
	 * @param EventListenerInterface $listener
	 * @param string $topic
	 * @return EventBrokerAbstract
	 * @throws \UnexpectedValueException
	 */
	public function registerListener(EventListenerInterface $listener, $topic)
	{
		if(!$this->hasTopic($topic)){
			$this->addTopic($topic);
		}
		$this->addEventListener($listener, $topic);
	    return $this;
	}
	
	/**
	 * 
	 * 
	 * @param EventListenerInterface $listener
	 * @param string $topic
	 * @return EventBrokerAbstract
	 * @throws \UnexpectedValueException
	 */
	public function unregisterListener(EventListenerInterface $listener, $topic)
	{
		if($this->hasTopic($topic) && $this->listenerHasTopic($listener, $topic)){
		    $this->removeEventListener($listener, $topic);
		}
		if(!$this->topicHasListeners($topic)){
			$this->removeTopic($topic);
		}
		return $this;
	}
	
	/**
	 * 
	 * 
	 * @param string $topic
	 * @return bool
	 * @throws \UnexpectedValueException
	 */
	public function hasTopic($topic)
	{
		return $this->validateTopicName($topic)
			->topicExists($topic);
	}
	
	/**
	 * 
	 * 
	 * @param string $topic
	 * @return EventBrokerAbstract
	 */
	protected function addTopic($topic)
	{
		if(!$this->topicExists($topic)){
			$this->listeners[$topic] = array();
		}
		return $this;
	}
	
	/**
	 * 
	 * 
	 * @param string $topic
	 * @return EventBrokerAbstract
	 */
	protected function removeTopic($topic)
	{
		unset($this->listeners[$topic]);
		return $this;
	}
	
	/**
	 * 
	 * 
	 * @param string $topic
	 * @return bool
	 */
	protected function topicExists($topic)
	{
		return array_key_exists($topic, $this->listeners);
	}
	
	/**
	 * 
	 * 
	 * @param string $topic
	 * @return bool
	 */
	protected function topicHasListeners($topic)
	{
		return (bool) count($this->listeners[$topic]);
	}
	
	/**
	 * 
	 * 
	 * @param EventEmitterInterface $emitter
	 * @return bool
	 */
	protected function hasEmitter(EventEmitterInterface $emitter)
	{
		return array_key_exists($this->getObjectHash($emitter), $this->emitters);
	}
	
	/**
	 * 
	 * 
	 * @param EventEmitterInterface $emitter
	 * @return EventBrokerAbstract
	 */
	protected function addEmitter(EventEmitterInterface $emitter)
	{
		$this->emitters[$this->getObjectHash($emitter)] = $emitter;
		return $this;
	}
	
	/**
	 * 
	 * 
	 * @param EventEmitterInterface $emitter
	 * @return EventBrokerAbstract
	 */
	protected function removeEmitter(EventEmitterInterface $emitter)
	{
		unset($this->emitters[$this->getObjectHash($emitter)]);
		return $this;
	}
	
	/**
	 * 
	 * 
	 * @param EventListenerInterface $listener
	 * @param string $topic
	 * @return EventBrokerAbstract
	 */
	protected function removeEventListener(EventListenerInterface $listener, $topic)
	{
		unset($this->listeners[$topic][$this->getObjectHash($listener)]);
		return $this;
	}
	
	/**
	 * 
	 * 
	 * @param EventListenerInterface $listener
	 * @param string $topic
	 * @return bool
	 */
	protected function listenerHasTopic(EventListenerInterface $listener, $topic)
	{
		return array_key_exists($this->getObjectHash($listener), $this->listeners[$topic]);
	}
	
	/**
	 * 
	 * 
	 * @param EventListenerInterface $listener
	 * @param string $topic
	 * @return EventBrokerAbstract
	 */
	protected function addEventListener(EventListenerInterface $listener, $topic)
	{
		$this->listeners[$topic][$this->getObjectHash($listener)] = $listener;
		return $this;
	}
	
	/**
	 * 
	 * 
	 * @param string $topic
	 * @return EventBrokerAbstract
	 * @throws \UnexpectedValueException
	 */
	protected function validateTopicName($topic)
	{
		if(!is_string($topic)){ 
	    	throw new \UnexpectedValueException('Topics should be identified by strings');
		}
		return $this;
	}
	
	/**
	 * 
	 * 
	 * @param mixed $object
	 * @return string
	 */
	protected function getObjectHash($object)
	{
		return spl_object_hash($object);
	}
}
