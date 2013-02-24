<?php
namespace DavideDebernardi\Happening\Tests;

use DavideDebernardi\Happening\EventBrokerInterface,
    DavideDebernardi\Happening\EventListenerInterface,
    DavideDebernardi\Happening\EventEmitterInterface,
    DavideDebernardi\Happening\EventInterface,
    DavideDebernardi\Happening\EventBrokerAbstract;

class EventBrokerAbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \UnexpectedValueException
     */
    public function testListeners()
    {
        $brokerMock = $this->getBrokerMock();
        $listenerMock = $this->getListenerMock();
        try{
            $brokerMock->registerListener($listenerMock, 'test-topic');
        } catch (Exception $e){
            $this->fail('EventBrokerAbstract cannot register listeners');
        }
        try{
            $brokerMock->unregisterListener($listenerMock, 'test-topic');
        } catch (Exception $e){
            $this->fail('EventBrokerAbstract cannot unregister listeners');
        }
        $brokerMock->registerListener($this->getListenerMock(), array());
    }
    
    public function testEmitters()
    {
        $brokerMock = $this->getBrokerMock();
        $emitterMock = $this->getEmitterMock();
        try{
            $brokerMock->registerEmitter($emitterMock, 'test-topic');
        } catch (Exception $e){
            $this->fail('EventBrokerAbstract cannot register emitters');
        }
        try{
            $brokerMock->unregisterEmitter($emitterMock, 'test-topic');
        } catch (Exception $e){
            $this->fail('EventBrokerAbstract cannot unregister emitters');
        }
    }
    
    /**
     * @expectedException \UnexpectedValueException
     */
    public function testHasTopic()
    {
        $brokerMock = $this->getBrokerMock();
        $listenerMock = $this->getListenerMock();
        $this->assertFalse(
            $brokerMock->hasTopic('test-topic'), 
            'EventBrokerAbstract thinks it has a non existent topic'
        );
        $brokerMock->registerListener($listenerMock, 'test-topic');
        $this->assertTrue(
            $brokerMock->hasTopic('test-topic'), 
            'EventBrokerAbstract does not know about an existing topic'
        );
        $brokerMock->unregisterListener($listenerMock, 'test-topic');
        $brokerMock->hasTopic(array());
    }
    
    public function testDispatchEvent()
    {
        $brokerMock = $this->getBrokerMock();
        $listenerMock = $this->getListenerMock();
        $emitterMock = $this->getEmitterMock();
        $eventMock = $this->getEventMock();
        
        //Simple case: one listener, no stop to propagation
        $brokerMock->registerListener($listenerMock, 'test-topic');
        $brokerMock->registerEmitter($emitterMock);
        $eventMock->expects($this->once())
            ->method('getTopic')
            ->will($this->returnValue('test-topic'));
        $listenerMock->expects($this->once())
            ->method('listenTo')
            ->with($eventMock);
        $brokerMock->dispatchEvent($emitterMock, $eventMock);
        $brokerMock->unregisterListener($listenerMock, 'test-topic');
        
        //Multiple listeners, no stop propagation
        $listeners = array();
        for($i = 0; $i<10; $i++){
            $listener = $this->getListenerMock();
            $listeners[] = $listener;
            $listener->expects($this->once())
                ->method('listenTo')
                ->with($eventMock);
            $brokerMock->registerListener($listener, 'test-topic');
        }
        $eventMock = $this->getEventMock();
        $eventMock->expects($this->once())
            ->method('getTopic')
            ->will($this->returnValue('test-topic'));
        $eventMock->expects($this->exactly(10))
            ->method('getStopPropagation')
            ->will($this->returnValue(false));
        $brokerMock->dispatchEvent($emitterMock, $eventMock);
        foreach($listeners as $listener){
            $brokerMock->unregisterListener($listener, 'test-topic');
        }
        
        //Multiple listeners, with stop propagation
        $listeners = array();
        for($i = 0; $i<10; $i++){
            $listener = $this->getListenerMock();
            $listeners[] = $listener;
            if($i < 5){
                $listener->expects($this->once())
                    ->method('listenTo')
                    ->with($eventMock);
            } else {
                $listener->expects($this->never())
                    ->method('listenTo');
            }
            $brokerMock->registerListener($listener, 'test-topic');
        }
        $eventMock = $this->getEventMock();
        $eventMock->expects($this->once())
            ->method('getTopic')
            ->will($this->returnValue('test-topic'));
        $stopPropagationCallback = function(){
            static $count;
            return ++$count >= 5;
        };
        $eventMock->expects($this->exactly(5))
            ->method('getStopPropagation')
            ->will($this->returnCallback($stopPropagationCallback));
        $brokerMock->dispatchEvent($emitterMock, $eventMock);
        
    }
    
    /**
     * @expectedException \RuntimeException
     */
    public function testDispatchEventWithNonRegisteredEmitter()
    {
        $this->getBrokerMock()->dispatchEvent($this->getEmitterMock(), $this->getEventMock());
    }
    
    /**
     * @expectedException \UnexpectedValueException
     */
    public function testDispatchEventFailsOnEventReturningNonStringTopic()
    {
        $brokerMock = $this->getBrokerMock();
        $eventMock = $this->getEventMock();
        $emitterMock = $this->getEmitterMock();
        
        $eventMock->expects($this->once())
            ->method('getTopic')
            ->will($this->returnValue(null));
            
        $brokerMock->registerEmitter($emitterMock);
        $brokerMock->dispatchEvent($emitterMock, $eventMock);
    }
    
    protected function getBrokerMock()
    {
        return $this->getMockForAbstractClass('DavideDebernardi\Happening\EventBrokerAbstract');
    }
    
    protected function getEmitterMock()
    {
        return $this->getMock('DavideDebernardi\Happening\EventEmitterInterface');
    }
    
    protected function getListenerMock()
    {
        return $this->getMock('DavideDebernardi\Happening\EventListenerInterface');
    }
    
    protected function getEventMock()
    {
        return $this->getMock('DavideDebernardi\Happening\EventInterface');
    }
}