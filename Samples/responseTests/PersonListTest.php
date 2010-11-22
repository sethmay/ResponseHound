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
 * a list of people using JSON. Also includes Samples of option usage.
 *
 * @category   Testing
 * @package    ResponseHound
 * @author     Seth May <seth@sethmay.net>
 * @copyright  2010 Seth May <seth@sethmay.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @since      Class available since Release 1.0
 */
class PersonListTest extends ResponseHound_JSONTestCase
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
     * Test Person->getList() via GET
     * The json data we're testing:
     *
     *    {
     *       "transactionId": 1,
     *       "status": "ok",
     *       "data":
     *       {
     *           "personList":
     *           [
     *               {"id": 1001, "firstName": "Seth", "lastName": "May", "gender": "male"},
     *               {"id": 1002, "firstName": "Erik", "lastName": "Barry", "gender": "male"},
     *               {"id": 1003, "firstName": "Megan", "lastName": "Forester", "gender": "female"},
     *               {"id": 1004, "firstName": "Stephanie", "lastName": "Regan", "gender": "female"},
     *               {"id": 1005, "firstName": "Michael", "lastName": "Roy", "gender": "male"},
     *               {"id": 1006, "firstName": "Amy", "lastName": "Boyd", "gender": "female"},
     *               {"id": 1007, "firstName": "Charity", "lastName": "Fiest", "gender": "female"}
     *           ]
     *       }
     *   }
     */
    public function test_getList_GET ()
    {
        $this->setBaseURL("http://localhost/libraries/ResponseHound/Samples/server/jsonGetServer.php")
             ->addRequestParam("action", "getList");

        $this->send();

        //var_dump($this->getResponse()->getData());
        // Test base elements
        $this->checkValue("transactionId"  ,""        ,"int"    ,array("value" => 1))
             ->checkValue("status"         ,""        ,"string" ,array("value" => "ok"))
             ->checkValue("data"           ,""        ,"array")
             ->checkValue("personList"     ,"data"    ,"array"  ,array("itemCount" => 7));


        // Test a group of elements
        $loc = "data.personList";
        $validGenders = array("male","female");
        $this->checkListValue("id"         ,$loc      ,"string")
             ->checkListValue("firstName"  ,$loc      ,"string")
             ->checkListValue("lastName"   ,$loc      ,"string")
             ->checkListValue("gender"     ,$loc      ,"string" ,array("valueIn"=>$validGenders));
    }


    /**
     * Test Person->getPerson() via POST
     */
    public function test_getPerson_POST ()
    {
        $this->setBaseURL("http://localhost/libraries/ResponseHound/Samples/server/jsonPostServer.php")
             ->addRequestParam("action", "getList");

        $this->sendPost();

        //var_dump($this->getResponse()->getData());
        // Test base elements
        $this->checkValue("transactionId"  ,""        ,"int"    ,array("value" => 1))
             ->checkValue("status"         ,""        ,"string" ,array("value" => "ok"))
             ->checkValue("data"           ,""        ,"array")
             ->checkValue("personList"     ,"data"    ,"array"  ,array("itemCount" => 7));


        // Test a group of elements
        $loc = "data.personList";
        $validGenders = array("male","female");
        $this->checkListValue("id"         ,$loc      ,"string")
             ->checkListValue("firstName"  ,$loc      ,"string")
             ->checkListValue("lastName"   ,$loc      ,"string")
             ->checkListValue("gender"     ,$loc      ,"string" ,array("valueIn"=>$validGenders));
    }
}