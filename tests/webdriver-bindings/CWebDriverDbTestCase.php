<?php
/*
  Copyright 2011 3e software house & interactive agency

  Licensed under the Apache License, Version 2.0 (the "License");
  you may not use this file except in compliance with the License.
  You may obtain a copy of the License at

  http://www.apache.org/licenses/LICENSE-2.0

  Unless required by applicable law or agreed to in writing, software
  distributed under the License is distributed on an "AS IS" BASIS,
  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
  See the License for the specific language governing permissions and
  limitations under the License.
 */

require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'phpwebdriver' . DIRECTORY_SEPARATOR . 'WebDriver.php' );

/**
*  Base class for functional tests using webdriver. 
*  It provides interface like classic selenium test class.  
*  @author kolec
*/
class CWebDriverDbTestCase extends CDbTestCase {
 
    public $baseUrl;
    /**
     * @var WebDriver this is the local copy of shared webdriver
     */
    protected $webdriver;
    /**
     * @var WebDriver this is the shared webdriver instance
     */
    private static $_webdriver;
    /**
     * @var string hostname where selenium server is run
     */
    protected static $host = 'localhost';
    /**
     * @var integer port on which selenium server listens
     */
    protected static $port = 4444;
    /**
     * @var string browser that we want to test against
     */
    protected static $browser = 'firefox';
 
    const LOAD_DELAY = 500000; //0.5 sec. delay
    const STEP_WAITING_TIME = 0.5; //when synchronous request is simulated this is single step waiting time
    const MAX_WAITING_TIME = 4; //when synchronous request is simulated this is total timeout when witing for result
 
    public static function setUpBeforeClass() {
        self::$_webdriver = new WebDriver( self::$host, self::$port );
        self::$_webdriver->connect( self::$browser );
    }
 
    public static function tearDownAfterClass() {
        if( self::$_webdriver ) {
            self::$_webdriver->close();
        }
    }
 
    public function setUp() {
        $this->webdriver = self::$_webdriver;
        parent::setUp();
    }
	
	public function setBrowserUrl( $url ) {
		$this->baseUrl = $url;
	}
	
	public function getBodyText() {
		$html = $this->webdriver->getPageSource();
		$body = preg_replace( '/(^(.*)<body[^>]*>)|(<\/body>(.*)$)/si', '', $html );
		return $body;
	}

	public function isTextPresent( $text ) {       
		$found = false;
		$i = 0;
		do {
			$html = $this->webdriver->getPageSource();
			if( is_string( $html ) ) {
				$found = !( strpos( $html, $text ) === false );
			}
			if( !$found ) {
				sleep( self::STEP_WAITING_TIME );
				$i += self::STEP_WAITING_TIME;
			}
		} while( !$found && $i <= self::MAX_WAITING_TIME );
		return $found;
	}

	public function __call( $name, $arguments ) {
		if( method_exists( $this->webdriver, $name ) ) {
			return call_user_func_array( array( $this->webdriver, $name ), $arguments );
		} else {
			return parent::__call( $name, $arguments );
		}
	}
}
