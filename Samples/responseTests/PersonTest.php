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

require_once("ResponseHound/JSONTestCase.php");


/**
 * A example of a test against a server that is providing data about
 * a person using JSON. Also includes examples of option usage.
 *
 * @category   Testing
 * @package    ResponseHound
 * @author     Seth May <seth@sethmay.net>
 * @copyright  2010 Seth May <seth@sethmay.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @since      Class available since Release 1.0
 */
class PersonTest extends ResponseHound_JSONTestCase
{

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp ()
    {
        parent::setUp();

        $this->addRequestParam("target", "person")
             ->addRequestParam("transactionId", 1);
    }


    /**
     * Test Person->getPerson() via GET
     * The json data we're testing:
     *   {
     *       "transactionId": 1,
     *       "status": "ok",
     *       "data":
     *       {
     *           "id": 1001,
     *           "firstName": "Seth",
     *           "lastName": "May",
     *           "phoneNumbers":
     *           [
     *               {"type": "cell", "number": "(321) 654-0987"},
     *               {"type": "home", "number": "(890) 567-1234"},
     *               {"type": "work", "number": "(456) 321-7890"}
     *           ],
     *           "address":
     *           {
     *               "number": "334",
     *               "street": "Adams Way",
     *               "city": "New York",
     *               "state": "NY",
     *               "postalCode": "10011"
     *           }
     *       }
     *   }
     */
    public function test_getPerson_GET ()
    {
        $this->setBaseURL("http://localhost/libraries/ResponseHound/Samples/server/jsonGetServer.php")
             ->addRequestParam("action", "getPerson")
             ->addRequestParam("id", '1001');

        $this->send();

        //var_dump($this->getResponse()->getData());
        // Test base elements
        $this->checkValue("transactionId"  ,""        ,"int"    ,array("value" => 1))
             ->checkValue("status"         ,""        ,"string" ,array("value" => "ok"))
             ->checkValue("data"           ,""        ,"array")
             ->checkValue("id"             ,"data"    ,"int"    ,array("value" => "1001"))
             ->checkValue("firstName"      ,"data"    ,"string" ,array("value" => "Seth"))
             ->checkValue("lastName"       ,"data"    ,"string" ,array("value" => "May"))
             ->checkValue("phoneNumbers"   ,"data"    ,"array")
             ->checkValue("address"        ,"data"    ,"array")
             ->checkValue("number"         ,"data.address"    ,"string" ,array("value" => "334"))
             ->checkValue("street"         ,"data.address"    ,"string" ,array("value" => "Adams Way"))
             ->checkValue("city"           ,"data.address"    ,"string" ,array("value" => "New York"))
             ->checkValue("state"          ,"data.address"    ,"string" ,array("value" => "NY"))
             ->checkValue("postalCode"     ,"data.address"    ,"string" ,array("value" => "10011"));


        // Test a group of elements
        $loc = "data.phoneNumbers";
        $validTypes = array("home","work","cell","fax");
        $this->checkListValue("type"    ,$loc      ,"string"  ,array("valueIn"=>$validTypes))
             ->checkListValue("number"  ,$loc      ,"string");
    }


    /**
     * Test Person->getPerson() via POST
     */
    public function test_getPerson_POST ()
    {
        $this->setBaseURL("http://localhost/libraries/ResponseHound/Samples/server/jsonPostServer.php")
             ->addRequestParam("action", "getPerson")
             ->addRequestParam("id", '1001');

        $this->sendPost();

        //var_dump($this->getRequestParams());
        //var_dump($this->getResponse()->getData());
        // Test base elements
        $this->checkValue("transactionId"  ,""        ,"int"    ,array("value" => 1))
             ->checkValue("status"         ,""        ,"string" ,array("value" => "ok"))
             ->checkValue("data"           ,""        ,"array")
             ->checkValue("id"             ,"data"    ,"int"    ,array("value" => "1001"))
             ->checkValue("firstName"      ,"data"    ,"string" ,array("value" => "Seth"))
             ->checkValue("lastName"       ,"data"    ,"string" ,array("value" => "May"))
             ->checkValue("phoneNumbers"   ,"data"    ,"array")
             ->checkValue("address"        ,"data"    ,"array")
             ->checkValue("number"         ,"data.address"    ,"string" ,array("value" => "334"))
             ->checkValue("street"         ,"data.address"    ,"string" ,array("value" => "Adams Way"))
             ->checkValue("city"           ,"data.address"    ,"string" ,array("value" => "New York"))
             ->checkValue("state"          ,"data.address"    ,"string" ,array("value" => "NY"))
             ->checkValue("postalCode"     ,"data.address"    ,"string" ,array("value" => "10011"));


        // Test a group of elements
        $loc = "data.phoneNumbers";
        $validTypes = array("home","work","cell","fax");
        $this->checkListValue("type"    ,$loc      ,"string"  ,array("valueIn"=>$validTypes))
             ->checkListValue("number"  ,$loc      ,"string");
    }    
}