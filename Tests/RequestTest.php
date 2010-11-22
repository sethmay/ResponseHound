<?php
/**
 * ResponseHound
 *
 * Copyright (c) 2010, Seth May <seth@sethmay.net>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Seth May nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   Testing
 * @package    ResponseHound
 * @author     Seth May <seth@sethmay.net>
 * @copyright  2010 Seth May <seth@sethmay.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://sethmay.net/ResponseHound
 * @since      File available since Release 1.0
 */

require_once 'PHPUnit/Framework/TestCase.php';
require_once 'ResponseHound/Request/Interface.php';
require_once 'ResponseHound/Request.php';

/**
 *
 *
 * @category   Testing
 * @package    ResponseHound
 * @author     Seth May <seth@sethmay.net>
 * @copyright  2010 Seth May <seth@sethmay.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @since      Class available since Release 1.0
 */
class ResponseHound_RequestTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {

    }

    
    public function testConstruct()
    {
        $actual = new ResponseHound_Request();

        $this->assertNull($this->readAttribute($actual, "url"));
        $this->assertNull($this->readAttribute($actual, "response"));
        $this->assertType("array", $this->readAttribute($actual, "params"));
    }


    public function testConstruct_WithParams()
    {
        $response = $this->getMock('ResponseHound_Response_Interface');

        $actual = new ResponseHound_Request("foo", $response, array("foo"=>"bar"));
        
        $this->assertEquals("foo", $actual->getURL());
        $this->assertType("ResponseHound_Response_Interface", $actual->getResponse());
        $params = $actual->getParams();
        $this->assertType("array", $params);
        $this->assertEquals("bar", $params["foo"]);
    }


    public function testAddParam ()
    {
        $actual = new ResponseHound_Request();
        $actual->addParam("foo", "bar");

        $params = $actual->getParams();
        $this->assertArrayHasKey("foo", $params);
        $this->assertEquals("bar", $params["foo"]);
    }


    public function testGetParam ()
    {
        $actual = new ResponseHound_Request(null, null, array("foo" => "bar"));
        $this->assertEquals("bar", $actual->getParam("foo"));
    }


    public function testGetParams ()
    {
        $actual = new ResponseHound_Request(null, null, array("foo" => "bar", "moo" => "nar"));
        $params = $actual->getParams();

        $this->assertArrayHasKey("foo", $params);
        $this->assertArrayHasKey("moo", $params);

        $this->assertEquals("bar", $params["foo"]);
        $this->assertEquals("nar", $params["moo"]);
    }


    public function testGetResponse ()
    {
        $response = $this->getMock('ResponseHound_Response_Interface');
        $actual = new ResponseHound_Request(null, $response);

        $this->assertType("ResponseHound_Response_Interface", $actual->getResponse());
        $this->assertEquals($response, $actual->getResponse());
    }


    public function testGetTransport ()
    {
        $transport = $this->getMock('ResponseHound_Transport_Interface');
        $actual = new ResponseHound_Request(null, null, array(), $transport);

        $this->assertType("ResponseHound_Transport_Interface", $actual->getTransport());
        $this->assertEquals($transport, $actual->getTransport());
    }


    public function testGetURL ()
    {
        $actual = new ResponseHound_Request("foo");
        $this->assertEquals("foo", $actual->getURL());
    }


    public function testSendGet ()
    {
        $json = "{'foo':'bar'}";

        $response = $this->getMock('ResponseHound_Response_Interface', array('getData', 'setData'));
        $response->expects($this->once())
                  ->method('setData')
                  ->will($this->returnValue($response))
                  //->with($this->equalTo($json));
                  ->with($this->anything());

        $transport = $this->getMock('ResponseHound_Transport_Interface', array('getResponseData'));
        $transport->expects($this->once())
                  ->method('getResponseData')
                  ->will($this->returnValue($json))
                  ->with($this->equalTo('foo'),
                        $this->equalTo('get'),
                        $this->anything());

        $actual = new ResponseHound_Request("foo", $response, array("foo"=>"bar"), $transport);
        $response1 = $actual->sendGet();

        $this->assertType("ResponseHound_Response_Interface", $response1);
    }


    public function testSendPost ()
    {
        $json = "{'foo':'bar'}";

        $response = $this->getMock('ResponseHound_Response_Interface', array('getData', 'setData'));
        $response->expects($this->once())
                  ->method('setData')
                  ->will($this->returnValue($response))
                  //->with($this->equalTo($json));
                  ->with($this->anything());

        $transport = $this->getMock('ResponseHound_Transport_Interface', array('getResponseData'));
        $transport->expects($this->once())
                  ->method('getResponseData')
                  ->will($this->returnValue($json))
                  ->with($this->equalTo('foo'),
                        $this->equalTo('post'),
                        $this->anything());

        $actual = new ResponseHound_Request("foo", $response, array("foo"=>"bar"), $transport);
        $response1 = $actual->sendPost();

        $this->assertType("ResponseHound_Response_Interface", $response1);
    }


    public function testSendRequest_Get ()
    {
        $json = "{'foo':'bar'}";

        $response = $this->getMock('ResponseHound_Response_Interface', array('getData', 'setData'));
        $response->expects($this->once())
                  ->method('setData')
                  ->will($this->returnValue($response))
                  //->with($this->equalTo($json));
                  ->with($this->anything());

        $transport = $this->getMock('ResponseHound_Transport_Interface', array('getResponseData'));
        $transport->expects($this->once())
                  ->method('getResponseData')
                  ->will($this->returnValue($json))
                  ->with($this->equalTo('foo'),
                        $this->equalTo('get'),
                        $this->anything());

        $actual = new ResponseHound_Request(null, $response, array(), $transport);
        $response1 = $actual->sendRequest('get', 'foo', array("foo"=>"bar"));

        $this->assertType("ResponseHound_Response_Interface", $response1);
    }


    public function testSendRequest_Post ()
    {
        $json = "{'foo':'bar'}";

        $response = $this->getMock('ResponseHound_Response_Interface', array('getData', 'setData'));
        $response->expects($this->once())
                  ->method('setData')
                  ->will($this->returnValue($response))
                  //->with($this->equalTo($json));
                  ->with($this->anything());

        $transport = $this->getMock('ResponseHound_Transport_Interface', array('getResponseData'));
        $transport->expects($this->once())
                  ->method('getResponseData')
                  ->will($this->returnValue($json))
                  ->with($this->equalTo('foo'),
                        $this->equalTo('post'),
                        $this->anything());

        $actual = new ResponseHound_Request(null, $response, array(), $transport);
        $response1 = $actual->sendRequest('post', 'foo', array("foo"=>"bar"));

        $this->assertType("ResponseHound_Response_Interface", $response1);
    }


    public function testSetParams ()
    {
        $actual = new ResponseHound_Request();
        $actual->setParams(array("foo"=>"bar", "moo"=>"nar"));
        $params = $actual->getParams();

        $this->assertArrayHasKey("foo", $params);
        $this->assertArrayHasKey("moo", $params);

        $this->assertEquals("bar", $params["foo"]);
        $this->assertEquals("nar", $params["moo"]);
    }


    public function testSetResponse ()
    {
        $response = $this->getMock('ResponseHound_Response_Interface');
        $actual = new ResponseHound_Request();
        $actual->setResponse($response);

        $this->assertType("ResponseHound_Response_Interface", $actual->getResponse());
        $this->assertEquals($response, $actual->getResponse());
    }


    public function testSetTransport ()
    {
        $transport = $this->getMock('ResponseHound_Transport_Interface');
        $actual = new ResponseHound_Request();
        $actual->setTransport($transport);

        $this->assertType("ResponseHound_Transport_Interface", $actual->getTransport());
        $this->assertEquals($transport, $actual->getTransport());
    }


    public function testSetURL ()
    {
        $actual = new ResponseHound_Request();
        $actual->setURL("fooBar1");

        $this->assertEquals("fooBar1", $actual->getURL());
    }
}
?>
