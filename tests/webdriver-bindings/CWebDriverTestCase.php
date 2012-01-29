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
class CWebDriverTestCase extends WebTestCase {

	protected $webdriver;
    public $baseUrl;
	
	const LOAD_DELAY = 500000; //0.5 sec. delay
    const STEP_WAITING_TIME = 0.5; //when synchronous request is simulated this is single step waiting time
    const MAX_WAITING_TIME = 4; //when synchronous request is simulated this is total timeout when witing for result

    protected function setUp( $host='localhost', $port=4444, $browser='firefox' ) {
        parent::setUp();
        $this->webdriver = new WebDriver( $host, $port );
        $this->webdriver->connect( $browser );
    }

    protected function tearDown() {
		if( $this->webdriver ) {
			$this->webdriver->close();
		}
    }
	
	public function setBrowserUrl( $url ) {
		$this->baseUrl = $url;
	}
	
    public function open( $url, $check_id = '' ) {
        $urlToOpen = $this->baseUrl . $url;

        $this->webdriver->get( $urlToOpen );
        if( !empty( $check_id ) ) {
            return $this->getElement( LocatorStrategy::id, $check_id );
		} else {
			usleep( self::LOAD_DELAY );
		}
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

    public function getAttribute( $xpath ) {
        $body = $this->getBodyText();
        $xml = new SimpleXMLElement( $body );
        $nodes = $xml->xpath( "$xpath" );
        return $nodes[0][0];
    }
	
    public function type( $element_name, $textToType ) {
        $element = $this->getElement( LocatorStrategy::id, $element_name );
        if( isset( $element ) ) {
            $element->sendKeys( array( $textToType ) );
        }
    }

    public function clear( $element_name ) {
        $element = $this->getElement( LocatorStrategy::id, $element_name );
        if( $element ) {
            $element->clear();
        }
    }
	
    public function submit( $element_name ) {
        $element = $this->getElement( LocatorStrategy::id, $element_name );
        if( isset( $element ) ) {
            $element->submit();
            usleep( self::LOAD_DELAY );
        }
    }

    public function click( $element_name ) {
        $element = $this->getElement( LocatorStrategy::id, $element_name );
        if( isset( $element ) ) {
            $element->click();
            usleep( self::LOAD_DELAY );
        }
    }

    public function select( $select_id, $option_text ) {
        $element = $this->getElement( LocatorStrategy::id, $select_id );
        $option = $element->findOptionElementByText( $option_text );
        $option->click();
    }

    public function getElementByIdOrName( $element_name ) {
        try {
            $element = $this->webdriver->findElementBy( LocatorStrategy::id, $element_name );
        } catch( NoSuchElementException $ex ) {
            $element = $this->webdriver->findElementBy( LocatorStrategy::name, $element_name );
        }
        return $element;
    }

    public function getElement( $strategy, $name ) {
        $i = 0;
        do {
            try {
                $element = $this->webdriver->findElementBy( $strategy, $name );
            } catch( NoSuchElementException $e ) {
                print_r( "\nWaiting for \"" . $name . "\" element to appear...\n" );
                sleep( self::STEP_WAITING_TIME );
                $i += self::STEP_WAITING_TIME;
            }
        } while( !isset( $element ) && $i <= self::MAX_WAITING_TIME );
        if( !isset( $element ) ) {
            $this->fail( "Element has not appeared after " . self::MAX_WAITING_TIME . " seconds." );
		}
        return $element;
    }

	public function __call( $name, $arguments ) {
		if( method_exists( $this->webdriver, $name ) ) {
			return call_user_func_array( array( $this->webdriver, $name ), $arguments );
		} else {
			return parent::__call( $name, $arguments );
		}
	}
}
