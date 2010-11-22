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

require_once "ResponseHound/Request.php";
require_once "ResponseHound/Response.php";

/**
 * A TestCase extension that provides functionality for testing
 * JSON based data sets. Through the use of requests and reponses,
 * this system can make requests to a source and test against the
 * returned data.
 *
 * @category   Testing
 * @package    ResponseHound
 * @author     Seth May <seth@sethmay.net>
 * @copyright  2010 Seth May <seth@sethmay.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @since      Class available since Release 1.0
 */
class ResponseHound_JSONTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $baseURL;

    /**
     * @var array
     */
    protected $requestParams;

    /**
     * @var ResponseHound_Request_Interface
     */
    protected $request;

    /**
     * @var ResponseHound_Response_Interface
     */
    protected $response;



    /**
     * Constructs a test case with the given name.
     *
     * @param  string $name
     * @param  array  $data
     * @param  string $dataName
     */
    public function __construct($name = NULL, array $data = array(), $dataName = '')
    {
        parent::__construct();

        $this->requestParams = array();
    }


    /**
     * @param string $name
     * @param mixed $type
     * @return ResponseHound_JSONTestCase
     */
    public function addRequestParam ($name, $value)
    {
        $this->requestParams[$name] = $value;
        return $this;
    }


    /**
     * Assumes that $location indicates a list (a group of items that
     * are all the same in structure, like the data for several different
     * kinds of cars). Specify one of the common list items (like "carName")
     * for the $name value.
     *
     * Acceptable options (same as checkValue except as listed):
     * 		valueList: Array - a supplied list of values to test. Each
     *          items in the group is tested against the matching
     *          indexed value.
     *
     * @param string $name
     * @param string $location delimited with '.'
     * @param string $type
     * @param array $options
     */
    public function checkListValue ($name, $location = "", $type = null, array $options = array())
    {
        $data = null;
        $valueList = null;

        if (array_key_exists("data", $options)) $data = $options["data"];
        if (array_key_exists("valueList", $options)) $valueList = $options["valueList"];

        $data = $this->getLocationData($location, $data);
        $c = 0;
        foreach ($data as $value)
        {
            if ($valueList)
            {
                if (!isset($valueList[$c]))
                {
                    $this->fail(
                    	"--Name: '$name'-- Data has more values (". count($data) .") than the supplied list: ".count($valueList[$c])."."
                    );
                }
                else
                {
                    $options["value"] = $valueList[$c];
                }
            }
            $options["data"] = $value;
            $this->checkValue($name, "", $type, $options);

            ++$c;
        }

        return $this;
    }


    /**
     * Test a value from the json response
     * Option values:
     * 		allowNull: Boolean (false) - indicates that the value can be null. If null, don't assertValue.
     * 		data: array - an alternative dataset to test against.
     * 		itemCount: Int - will test to verify that the array item has the specified number of values.
     * 		mustExist: Boolean (true) - indicates that the item must exist. If false, having the item missing won't fail.
     * 		value: Mixed - will test to verify that the item is equivilant to the value. This also works with arrays.
     * 		valueIn: Array - test to see if the value is in the given array.
     *
     * @param string $name
     * @param string $location delimited with '.'
     * @param string $type
     * @param array $options
     */
    public function checkValue ($name, $location = "", $type = null, array $options = array())
    {
        // Parse Options
        $allowNull = false;
        $data      = null;
        $itemCount = null;
        $mustExist = true;
        $value     = null;
        $valueIn   = null;

        if (array_key_exists("allowNull", $options)) $allowNull = $options["allowNull"];
        if (array_key_exists("data", $options))      $data      = $options["data"];
        if (array_key_exists("itemCount", $options)) $itemCount = $options["itemCount"];
        if (array_key_exists("mustExist", $options)) $mustExist = $options["mustExist"];
        if (array_key_exists("value", $options))     $value     = $options["value"];
        if (array_key_exists("valueIn", $options))   $valueIn   = $options["valueIn"];

        // Get just the portion of data that we need (the correct level)
        $data = $this->getLocationData($location, $data);

        // Test the data.
        if ($mustExist || (!$mustExist && array_key_exists($name, $data)))
        {
            $this->assertArrayHasKey($name, $data, "--Name: '$name'--");

            if (!$allowNull)
            {
                $this->assertNotNull($data[$name], "--Name: '$name'--");
            }
            if ($data[$name] !== null)
            {
                if ($type !== null)
                {
                    $this->assertType($type, $data[$name], "--Name: '$name'--");
                }

                if ($value)
                {
                    $this->assertEquals($value, $data[$name], "--Name: '$name'--");
                }

                if ($valueIn)
                {
                    $pass = false;
                    $values = "";
                    $c=0;
                    foreach ($valueIn as $value)
                    {
                        if ($c > 0) $values .= ", ";
                        $values .= $value;
                        ++$c;

                        if ($data[$name] == $value) $pass = true;
                    }

                    if (!$pass)
                    {
                        $this->fail(
                            "--Name: '$name'-- Value (". $data[$name] .") is not in supplied list of acceptable values: ($values)."
                        );
                    }
                }

                if ($itemCount !== null)
                {
                    if (!is_array($data[$name]))
                    {
                        $this->fail(
                            "--Name: '$name'-- Only arrays can be tested for an item count."
                        );
                    }

                    if (count($data[$name]) != $itemCount)
                    {
                        $this->fail(
                            "--Name: '$name'-- Item count (". count($data[$name]) .") does not equal expected count: $itemCount."
                        );
                    }
                }
            }
        }

        return $this;
    }


    /**
     * @return string
     */
    public function getBaseURL ()
    {
        return $this->baseURL;
    }


    /**
     * Test a value from the json response
     *
     * @param string $location delimited with '.'
     * @return array
     */
    protected function getLocationData ($location = "", $data = null)
    {
        if ($data === null)
        {
            $data = $this->getResponseData();
        }

        //Get just the portion of data that we need (the correct level)
        if (trim($location))
        {
            $levels = explode(".", trim($location));
            foreach ($levels as $level)
            {
                if (array_key_exists($level, $data))
                {
                    $data = $data[$level];
                }
                else
                {
                    $e = new \Exception();

                    $this->fail(
                    	"Location doesn't exist " . $location
                    	. "<br/>File: " . $e->getFile()
                        . "<br/>Line: " . $e->getLine()
                        . "<br/>Trace: " . $e->getTraceAsString()
                        . "</br>"
                    );
//                    $this->syntheticFail(
//                    	 "Location doesn't exist " . $location
//                        ,$e->getFile()
//                        ,$e->getLine()
//                        ,$e->getTrace()
//                    );
                }
            }
        }

        return $data;
    }


    /**
     * @return ResponseHound_Request_Interface
     */
    public function getRequest ()
    {
        return $this->request;
    }


    /**
     * @return array
     */
    public function getRequestParams ()
    {
        return $this->requestParams;
    }


	/**
     * @return ResponseHound_Response_Interface
     */
    public function getResponse ()
    {
        return $this->response;
    }


	/**
     * @return array
     */
    public function getResponseData ()
    {
        if ($this->getResponse())
        {
            return $this->getResponse()->getData();
        }
        return null;
    }


    /**
     * Alias for sendGetRequest
     *
     * @return ResponseHound_JSONTestCase
     */
    public function send ()
    {
        return $this->sendGetRequest();
    }


    /**
     * Alias for sendGetRequest
     *
     * @return ResponseHound_JSONTestCase
     */
    public function sendGet ()
    {
        return $this->sendGetRequest();
    }


    /**
     * Instructs the request to send itself as a get
     *
     * @return ResponseHound_JSONTestCase
     */
    public function sendGetRequest ()
    {
        $this->getRequest()
             ->setURL($this->getBaseURL())
             ->setParams($this->requestParams);

        try
        {
            $this->getRequest()->sendGet();
        }
        catch (Exception $e)
        {
            throw $e;
        }

        $this->setResponse($this->getRequest()->getResponse());

        return $this;
    }


    /**
     * Alias for sendPostRequest
     *
     * @return ResponseHound_JSONTestCase
     */
    public function sendPost ()
    {
        return $this->sendPostRequest();
    }


    /**
     * Instructs the request to send itself as a post
     *
     * @return ResponseHound_JSONTestCase
     */
    public function sendPostRequest ()
    {
        $this->getRequest()
             ->setURL($this->getBaseURL())
             ->setParams($this->requestParams);

        try
        {
            $this->getRequest()->sendPost();
        }
        catch (Exception $e)
        {
            throw $e;
        }

        $this->setResponse($this->getRequest()->getResponse());

        return $this;
    }


    /**
     * @param string $baseURL
     * @return ResponseHound_JSONTestCase
     */
    public function setBaseURL ($baseURL)
    {
        $this->baseURL = $baseURL;
        return $this;
    }


	/**
     * @param ResponseHound_Request_Interface $request
     * @return ResponseHound_JSONTestCase
     */
    public function setRequest (ResponseHound_Request_Interface $request)
    {
        $this->request = $request;
        return $this;
    }


    /**
     * @param array $params
     * @return ResponseHound_JSONTestCase
     */
    public function setRequestParams (array $params = array())
    {
        $this->requestParams = $params;
        return $this;
    }


	/**
     * @param ResponseHound_Response_Interface $response
     * @return ResponseHound_JSONTestCase
     */
    public function setResponse (ResponseHound_Response_Interface $response)
    {
        $this->response = $response;
        return $this;
    }


    /**
     * Performs operation returned by getSetUpOperation().
     */
    protected function setUp()
    {
        parent::setUp();

        if (!$this->getRequest())
        {
            $this->setRequest(new ResponseHound_Request());
        }
    }


    /**
     * Performs operation returned by getSetUpOperation().
     */
    protected function tearDown()
    {
        parent::tearDown();

        unset($this->requestParams);
        unset($this->baseURL);
        unset($this->request);
        unset($this->response);
    }
}