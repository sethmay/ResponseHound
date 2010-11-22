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
require_once 'ResponseHound/Response/Interface.php';
require_once 'ResponseHound/Request/Interface.php';
require_once 'ResponseHound/JSONTestCase.php';

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
class ResponseHound_JSONTestCaseTest extends PHPUnit_Framework_TestCase
{
    public function testConstruct($name = NULL, array $data = array(), $dataName = '')
    {
        $actual = new ResponseHound_JSONTestCase();

        $this->assertType("array", $this->readAttribute($actual, "requestParams"));
    }


    public function testAddRequestParam ()
    {
        $actual = new ResponseHound_JSONTestCase();
        $actual->addRequestParam("foo", "bar");
        $params = $this->readAttribute($actual, "requestParams");

        $this->assertArrayHasKey("foo", $params);
        $this->assertEquals("bar", $params["foo"]);
    }


    public function testGetBaseURL ()
    {
        $actual = new ResponseHound_JSONTestCase();
        $this->assertNull($actual->getBaseURL());

        $actual->setBaseURL("foo");
        $this->assertEquals("foo", $actual->getBaseURL());
    }


    public function testGetRequest ()
    {
        $request = $this->getMock('ResponseHound_Request_Interface');
        $actual = new ResponseHound_JSONTestCase();
        $this->assertNull($actual->getRequest());

        $actual->setRequest($request);
        $this->assertEquals($request, $actual->getRequest());
    }


    public function testGetResponse ()
    {
        $response = $this->getMock('ResponseHound_Response_Interface');
        $actual = new ResponseHound_JSONTestCase();
        $this->assertNull($actual->getResponse());

        $actual->setResponse($response);
        $this->assertEquals($response, $actual->getResponse());
    }


    public function testGetResponseData ()
    {
        $json = "{'foo':'bar'}";

        $response = $this->getMock('ResponseHound_Response_Interface', array('getData', 'setData'));
        $response->expects($this->once())
                  ->method('getData')
                  ->will($this->returnValue($json));

        $actual = new ResponseHound_JSONTestCase();
        $this->assertNull($actual->getResponseData());

        $actual->setResponse($response);
        $this->assertEquals($json, $actual->getResponseData());
    }


    public function testSend ()
    {
        $url = "foo";
        $params = array("foo"=>"bar", "moo"=>"nar");
        $data = array("soo"=>"tar", "voo"=>"war");

        $response = $this->getMock('ResponseHound_Response_Interface', array('getData', 'setData'));

        $request = $this->getMock('ResponseHound_Request_Interface');
        $request->expects($this->once())
                ->method('setURL')
                ->will($this->returnValue($request))
                ->with($this->equalTo($url));
        $request->expects($this->once())
                ->method('setParams')
                ->will($this->returnValue($request))
                ->with($this->equalTo($params));
        $request->expects($this->once())
                ->method('sendGet');
        $request->expects($this->once())
                ->method('getResponse')
                ->will($this->returnValue($response));

        $actual = new ResponseHound_JSONTestCase();
        $actual->setBaseURL($url)
               ->setRequestParams($params)
               ->setRequest($request);

        $obj = $actual->send();

        $this->assertEquals($actual, $obj);
        $this->assertEquals($response, $actual->getResponse());
    }


    public function testSendGet ()
    {
        $url = "foo";
        $params = array("foo"=>"bar", "moo"=>"nar");
        $data = array("soo"=>"tar", "voo"=>"war");

        $response = $this->getMock('ResponseHound_Response_Interface', array('getData', 'setData'));

        $request = $this->getMock('ResponseHound_Request_Interface');
        $request->expects($this->once())
                ->method('setURL')
                ->will($this->returnValue($request))
                ->with($this->equalTo($url));
        $request->expects($this->once())
                ->method('setParams')
                ->will($this->returnValue($request))
                ->with($this->equalTo($params));
        $request->expects($this->once())
                ->method('sendGet');
        $request->expects($this->once())
                ->method('getResponse')
                ->will($this->returnValue($response));

        $actual = new ResponseHound_JSONTestCase();
        $actual->setBaseURL($url)
               ->setRequestParams($params)
               ->setRequest($request);

        $obj = $actual->sendGet();

        $this->assertEquals($actual, $obj);
        $this->assertEquals($response, $actual->getResponse());
    }


    public function testSendGetRequest ()
    {
        $url = "foo";
        $params = array("foo"=>"bar", "moo"=>"nar");
        $data = array("soo"=>"tar", "voo"=>"war");

        $response = $this->getMock('ResponseHound_Response_Interface', array('getData', 'setData'));

        $request = $this->getMock('ResponseHound_Request_Interface');
        $request->expects($this->once())
                ->method('setURL')
                ->will($this->returnValue($request))
                ->with($this->equalTo($url));
        $request->expects($this->once())
                ->method('setParams')
                ->will($this->returnValue($request))
                ->with($this->equalTo($params));
        $request->expects($this->once())
                ->method('sendGet');
        $request->expects($this->once())
                ->method('getResponse')
                ->will($this->returnValue($response));

        $actual = new ResponseHound_JSONTestCase();
        $actual->setBaseURL($url)
               ->setRequestParams($params)
               ->setRequest($request);

        $obj = $actual->sendGetRequest();

        $this->assertEquals($actual, $obj);
        $this->assertEquals($response, $actual->getResponse());
    }


    public function testSendPost ()
    {
        $url = "foo";
        $params = array("foo"=>"bar", "moo"=>"nar");
        $data = array("soo"=>"tar", "voo"=>"war");

        $response = $this->getMock('ResponseHound_Response_Interface', array('getData', 'setData'));

        $request = $this->getMock('ResponseHound_Request_Interface');
        $request->expects($this->once())
                ->method('setURL')
                ->will($this->returnValue($request))
                ->with($this->equalTo($url));
        $request->expects($this->once())
                ->method('setParams')
                ->will($this->returnValue($request))
                ->with($this->equalTo($params));
        $request->expects($this->once())
                ->method('sendPost');
        $request->expects($this->once())
                ->method('getResponse')
                ->will($this->returnValue($response));

        $actual = new ResponseHound_JSONTestCase();
        $actual->setBaseURL($url)
               ->setRequestParams($params)
               ->setRequest($request);

        $obj = $actual->sendPost();

        $this->assertEquals($actual, $obj);
        $this->assertEquals($response, $actual->getResponse());
    }


    public function testSendPostRequest ()
    {
        $url = "foo";
        $params = array("foo"=>"bar", "moo"=>"nar");
        $data = array("soo"=>"tar", "voo"=>"war");

        $response = $this->getMock('ResponseHound_Response_Interface', array('getData', 'setData'));

        $request = $this->getMock('ResponseHound_Request_Interface');
        $request->expects($this->once())
                ->method('setURL')
                ->will($this->returnValue($request))
                ->with($this->equalTo($url));
        $request->expects($this->once())
                ->method('setParams')
                ->will($this->returnValue($request))
                ->with($this->equalTo($params));
        $request->expects($this->once())
                ->method('sendPost');
        $request->expects($this->once())
                ->method('getResponse')
                ->will($this->returnValue($response));

        $actual = new ResponseHound_JSONTestCase();
        $actual->setBaseURL($url)
               ->setRequestParams($params)
               ->setRequest($request);

        $obj = $actual->sendPostRequest();

        $this->assertEquals($actual, $obj);
        $this->assertEquals($response, $actual->getResponse());
    }


    public function testSetBaseURL ()
    {
        $actual = new ResponseHound_JSONTestCase();
        $obj = $actual->setBaseURL("fooBar1");

        $this->assertEquals($actual, $obj);
        $this->assertEquals("fooBar1", $actual->getBaseURL());
    }


    public function testSetRequest ()
    {
        $request = $this->getMock('ResponseHound_Request_Interface');
        $actual = new ResponseHound_JSONTestCase();
        $obj = $actual->setRequest($request);

        $this->assertEquals($actual, $obj);
        $this->assertEquals($request, $actual->getRequest());
    }


    public function testSetRequestParams ()
    {
        $actual = new ResponseHound_JSONTestCase();
        $obj = $actual->setRequestParams(array("foo"=>"bar"));

        $this->assertEquals($actual, $obj);
        $this->assertEquals(array("foo"=>"bar"), $actual->getRequestParams());
    }


    public function testSetResponse ()
    {
        $response = $this->getMock('ResponseHound_Response_Interface');
        $actual = new ResponseHound_JSONTestCase();
        $obj = $actual->setResponse($response);

        $this->assertEquals($actual, $obj);
        $this->assertEquals($response, $actual->getResponse());
    }


    public function testCheckListValue ()
    {
        $data = array(
             "foo"=>"bar",
             "data"=>array(
                  array("moo"=>"nar","woo"=>"yar")
                 ,array("moo"=>"oar","woo"=>"yar")
                 ,array("moo"=>"par","woo"=>"yar")
         ));

        $response = $this->getMock('ResponseHound_Response_Interface', array('getData', 'setData'));
        $response->expects($this->exactly(2))
                 ->method('getData')
                 ->will($this->returnValue($data));

        $actual = new ResponseHound_JSONTestCase();
        $actual->setResponse($response);

        $obj = $actual->checkListValue("moo", "data", "string");
        $this->assertEquals($actual, $obj);
        $obj = $actual->checkListValue("woo", "data", "string");
        $this->assertEquals($actual, $obj);
    }


    public function testCheckListValue_optionValueList ()
    {
        $data = array(
            "foo"=>"bar",
            "data"=>array(
                  array("moo"=>"nar","woo"=>"var")
                 ,array("moo"=>"oar","woo"=>"yar")
                 ,array("moo"=>"par","woo"=>"zar")
        ));

        $response = $this->getMock('ResponseHound_Response_Interface', array('getData', 'setData'));
        $response->expects($this->exactly(2))
                 ->method('getData')
                 ->will($this->returnValue($data));

        $actual = new ResponseHound_JSONTestCase();
        $actual->setResponse($response);

        $obj = $actual->checkListValue("moo", "data", "string", array("valueList"=>array("nar","oar","par")));
        $this->assertEquals($actual, $obj);
        $obj = $actual->checkListValue("woo", "data", "string", array("valueList"=>array("var","yar","zar")));
        $this->assertEquals($actual, $obj);
    }


    public function testCheckValue ()
    {
        $data = array(
            "foo"=>"bar",
            "data"=>array("moo"=>"nar","woo"=>"var")
        );

        $response = $this->getMock('ResponseHound_Response_Interface', array('getData', 'setData'));
        $response->expects($this->exactly(3))
                 ->method('getData')
                 ->will($this->returnValue($data));

        $actual = new ResponseHound_JSONTestCase();
        $actual->setResponse($response);

        $obj = $actual->checkValue("foo", "", "string");
        $this->assertEquals($actual, $obj);
        $obj = $actual->checkValue("moo", "data", "string");
        $this->assertEquals($actual, $obj);
        $obj = $actual->checkValue("woo", "data", "string");
        $this->assertEquals($actual, $obj);
    }


    public function testCheckValue_optionAllowNull ()
    {
        $data = array(
            "foo"=>"bar",
            "data"=>array("moo"=>null,"woo"=>null)
        );

        $response = $this->getMock('ResponseHound_Response_Interface', array('getData', 'setData'));
        $response->expects($this->exactly(2))
                 ->method('getData')
                 ->will($this->returnValue($data));

        $actual = new ResponseHound_JSONTestCase();
        $actual->setResponse($response);

        $obj = $actual->checkValue("moo", "data", "string", array("allowNull"=>true));
        $this->assertEquals($actual, $obj);

        try
        {
            $obj = $actual->checkValue("woo", "data", "string");
            $this->assertFalse(true, "Should not have gotten here");
        }
        catch(PHPUnit_Framework_AssertionFailedError $e)
        {
            $this->assertTrue(true, "Win!");
        }
    }


    public function testCheckValue_optionData ()
    {
        $data = array(
            "foo"=>"bar",
            "data"=>array("moo"=>"nar", "woo"=>"yar")
        );

        $response = $this->getMock('ResponseHound_Response_Interface', array('getData', 'setData'));

        $actual = new ResponseHound_JSONTestCase();
        $actual->setResponse($response);

        $obj = $actual->checkValue("moo", "data", "string", array("data"=>$data));
        $this->assertEquals($actual, $obj);
    }


    public function testCheckValue_optionItemCount ()
    {
        $data = array(
            "foo"=>"bar",
            "data"=>array("moo"=>"nar", "woo"=>"yar")
        );

        $response = $this->getMock('ResponseHound_Response_Interface', array('getData', 'setData'));
        $response->expects($this->exactly(1))
                 ->method('getData')
                 ->will($this->returnValue($data));

        $actual = new ResponseHound_JSONTestCase();
        $actual->setResponse($response);

        $obj = $actual->checkValue("data", "", "array", array("itemCount"=>2));
        $this->assertEquals($actual, $obj);
    }


    public function testCheckValue_optionMustExist ()
    {
        $data = array(
            "foo"=>"bar",
            "data"=>array()
        );

        $response = $this->getMock('ResponseHound_Response_Interface', array('getData', 'setData'));
        $response->expects($this->exactly(2))
                 ->method('getData')
                 ->will($this->returnValue($data));

        $actual = new ResponseHound_JSONTestCase();
        $actual->setResponse($response);

        $obj = $actual->checkValue("moo", "data", "string", array("mustExist"=>false));
        $this->assertEquals($actual, $obj);

        try
        {
            $obj = $actual->checkValue("woo", "data", "string");
            $this->assertFalse(true, "Should not have gotten here");
        }
        catch(PHPUnit_Framework_AssertionFailedError $e)
        {
            $this->assertTrue(true, "Win!");
        }
    }


    public function testCheckValue_optionValue ()
    {
        $data = array(
            "foo"=>"bar",
            "data"=>array("moo"=>"nar","woo"=>"yar")
        );

        $response = $this->getMock('ResponseHound_Response_Interface', array('getData', 'setData'));
        $response->expects($this->exactly(4))
                 ->method('getData')
                 ->will($this->returnValue($data));

        $actual = new ResponseHound_JSONTestCase();
        $actual->setResponse($response);

        $obj = $actual->checkValue("foo", "", "string", array("value"=>"bar"));
        $this->assertEquals($actual, $obj);

        $obj = $actual->checkValue("data", "", "array", array("moo"=>"nar","woo"=>"yar"));
        $this->assertEquals($actual, $obj);

        try
        {
            $obj = $actual->checkValue("foo", "", "string", array("value"=>"car"));
            $this->assertFalse(true, "Should not have gotten here");
        }
        catch(PHPUnit_Framework_AssertionFailedError $e)
        {
            $this->assertTrue(true, "Win!");
        }

        try
        {
            $obj = $actual->checkValue("data", "", "array", array("moo"=>"nar"));
            $this->assertFalse(true, "Should not have gotten here");
        }
        catch(PHPUnit_Framework_AssertionFailedError $e)
        {
            $this->assertTrue(true, "Win!");
        }

    }


    public function testCheckValue_optionValueIn ()
    {
        $data = array(
            "foo"=>"bar",
            "moo"=>"nar",
            "woo"=>"var"
        );

        $response = $this->getMock('ResponseHound_Response_Interface', array('getData', 'setData'));
        $response->expects($this->exactly(3))
                 ->method('getData')
                 ->will($this->returnValue($data));

        $actual = new ResponseHound_JSONTestCase();
        $actual->setResponse($response);

        $obj = $actual->checkValue("foo", "", "string", array("valueIn"=>array("nar","bar","yar")));
        $this->assertEquals($actual, $obj);

        $obj = $actual->checkValue("moo", "", "string", array("valueIn"=>array("nar","bar","yar")));
        $this->assertEquals($actual, $obj);

        try
        {
            $obj = $actual->checkValue("woo", "", "string", array("valueIn"=>array("nar","bar","yar")));
            $this->assertFalse(true, "Should not have gotten here");
        }
        catch(PHPUnit_Framework_AssertionFailedError $e)
        {
            $this->assertTrue(true, "Win!");
        }
    }
}
