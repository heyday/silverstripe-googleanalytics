<?php

use UnitedPrototype\GoogleAnalytics;

class TrackerTest extends \PHPUnit_Framework_TestCase
{
    protected $tracker;
    public function setUp()
    {   

        SSGoogleAnalytics::setDomain('test.net.nz');
        SSGoogleAnalytics::setTrackingCode('UA-11111111-1');
        SSGoogleAnalytics::setLoggingCallback(function($request) {
            return $request;
        });

        $this->tracker = new SSGoogleAnalytics();

    }
    
    public function testDomain()
    {

        $this->assertEquals(
            SSGoogleAnalytics::getDomain(),
            'test.net.nz'
        );

    }

    public function testTrackingCode()
    {

        $this->assertEquals(
            SSGoogleAnalytics::getTrackingCode(),
            'UA-11111111-1'
        );

    }

    public function testLoggingCallback()
    {
        $callback = SSGoogleAnalytics::getLoggingCallback();

        $this->assertEquals(
            get_class($callback),
            'Closure'
        );

        $this->assertEquals(
            $callback('testLoggingCallback'),
            'testLoggingCallback'
        );

    }

    public function testTracker()
    {
        $tracker = $this->tracker->getGATracker();

        $this->assertEquals(
            $this->tracker->getGATracker()->getAccountId(),
            'UA-11111111-1'
        );

        $this->assertEquals(
            $this->tracker->getGATracker()->getDomainName(),
            'test.net.nz'
        );

    }

}