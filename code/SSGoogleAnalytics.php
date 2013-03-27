<?php

use UnitedPrototype\GoogleAnalytics;

use Cookie as SSCookie;
use Session as SSSession;

class SSGoogleAnalytics
{
    /**
     * The tracking code to use
     * @var string
     */
    private static $trackingCode;
    /**
     * The domain to use
     * @var string
     */
    private static $domain;
    /**
     * The logging callback to use
     * @var string
     */
    private static $loggingCallback;
    /**
     * An instance of GoogleAnalytics\Tracker
     * @var GoogleAnalytics\Tracker
     */
    private $gaTracker;
    /**
     * An instance of GoogleAnalytics\Session
     * @var GoogleAnalytics\Session
     */
    private $gaSession;
    /**
     * An instance of GoogleAnalytics\Visitor
     * @var GoogleAnalytics\Visitor
     */
    private $gaVisitor;
    /**
     * Creates the object using the statically configured tracking code and domain
     */
    public function __construct()
    {        
        if (!self::$trackingCode || !self::$domain) {            
            throw new RuntimeException('Please set a tracking code and domain for this analytics instance.');            
        }        
    }
    /**
     * Set the tracking code to use
     * @param strin $trackingCode The tracking code
     */
    public static function setTrackingCode($trackingCode) 
    {
        self::$trackingCode = $trackingCode;
    }
    /**
     * Gets the tracking code in use
     * @return string The tracking code
     */
    public static function getTrackingCode()
    {
        return self::$trackingCode;
    }
    /**
     * Set the domain to use
     * @param string $domain The domain to use
     */
    public static function setDomain($domain) 
    {
        self::$domain = $domain;
    }
    /**
     * Gets the domain in use
     * @return string The domain
     */
    public static function getDomain()
    {
        return self::$domain;
    }
    /**
     * Sets logging callback to use
     * @param string $loggingCallback Logging callback
     */
    public static function setLoggingCallback($loggingCallback) 
    {
        self::$loggingCallback = $loggingCallback;
    }
    /**
     * Gets the logging callback 
     * @return string The logging callback
     */
    public static function getLoggingCallback()
    {
        return self::$loggingCallback;
    }
    /**
     * Get a GA tracker instance setting one with defaults if one doesn't exist
     * @return GoogleAnalytics\Tracker The GA tracker
     */
    public function getGATracker()
    {   
        if (null == $this->gaTracker) {
            $this->setGATracker(
                new GoogleAnalytics\Tracker(
                    self::$trackingCode,
                    self::$domain,
                    new GoogleAnalytics\Config(
                        array(
                            'AnonymizeIpAddresses' => true,
                            'ErrorSeverity' => GoogleAnalytics\Config::ERROR_SEVERITY_SILENCE,
                            'FireAndForget' => true,
                            'LoggingCallback' => self::$loggingCallback
                        )
                    )
                )
            );
        }        
        return $this->gaTracker;
    }
    /**
     * Set a GA tracker
     * @param GoogleAnalytics\Tracker $tracker The tracker
     */
    public function setGATracker(GoogleAnalytics\Tracker $tracker) 
    {
        $this->gaTracker = $tracker;
    }
    /**
     * Get a GA visitor, if one doesn't exist build one and set it from the utma cookie
     * @return GoogleAnalytics\Visitor The visitor
     */
    public function getGAVisitor()
    {
        if (null == $this->gaVisitor) {
            $visitor = new GoogleAnalytics\Visitor();
            if (SSCookie::get('__utma')) {
                $visitor->fromUtma(SSCookie::get('__utma'));
            }
            $visitor->fromServerVar($_SERVER);
            $visitor->addSession($this->getGASession());
            $this->setGAVisitor($visitor);
        }
        
        return $this->gaVisitor;        
    }
    /**
     * Set a GA Visitor
     * @param GoogleAnalytics\Visitor $visitor [description]
     */
    public function setGAVisitor(GoogleAnalytics\Visitor $visitor)
    {
        $this->gaVisitor = $visitor;        
    }
    /**
     * Get a GA Session, if one doesn't exist in on the object or in the session, then build a new one from the utmb cookie
     * @return GoogleAnalytics\Session The GA Session
     */
    public function getGASession()
    {        
        if (null == $this->gaSession) {
            $sessionRaw = SSSession::get('SSGA_Session');
            $session = $sessionRaw ? unserialize(SSSession::get('SSGA_Session')) : false;
            if (!$session instanceof GoogleAnalytics\Session) {
                $session = new GoogleAnalytics\Session();
                if (Cookie::get('__utmb')) {
                    $session->fromUtmb(Cookie::get('__utmb'));
                }
            }
            $this->setGASession($session);
        }
        return $this->gaSession;        
    }
    /**
     * Set a GA Session to the object and the silverstripe php session
     * @param GoogleAnalytics\Session $session The GA session to set
     */
    public function setGASession(GoogleAnalytics\Session $session)
    {
        $this->gaSession = $session;
        SSSession::set('SSGA_Session', serialize($session));
    }
    /**
     * Track a page view using the Visitor and Session from this object
     * @param  GoogleAnalytics\Page $page The page view to track
     * @return null
     */
    public function trackPageview(GoogleAnalytics\Page $page)
    {   
        $this->getGATracker()->trackPageview(
            $page,
            $this->getGASession(),
            $this->getGAVisitor()
        );
    }
    /**
     * Track an event using the Visitor and Session from this object
     * @param  GoogleAnalytics\Event $page The event to track
     * @return null
     */
    public function trackEvent(GoogleAnalytics\Event $event)
    {
        $this->getGATracker()->trackEvent(
            $event,
            $this->getGASession(),
            $this->getGAVisitor()
        );
    }
    /**
     * Track a transaction using the Visitor and Session from this object
     * @param  GoogleAnalytics\Transaction $page The transation to track
     * @return null
     */
    public function trackTransaction(GoogleAnalytics\Transaction $transaction)
    {   
        $this->getGATracker()->trackTransaction(
            $transaction,
            $this->getGASession(),
            $this->getGAVisitor()
        );
    }    
}
