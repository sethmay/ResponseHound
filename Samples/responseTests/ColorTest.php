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
 * a colors using JSON. Also includes Samples of option usage.
 *
 * @category   Testing
 * @package    ResponseHound
 * @author     Seth May <seth@sethmay.net>
 * @copyright  2010 Seth May <seth@sethmay.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @since      Class available since Release 1.0
 */
class ColorTest extends ResponseHound_JSONTestCase
{

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp ()
    {
        parent::setUp();

        $this->addRequestParam("target", "colors")
             ->addRequestParam("transactionId", 1);
    }


    /**
     * Prepares the environment before running a test.
     */
    protected function tearDown ()
    {
        parent::tearDown();
    }


    /**
     * Test colors->status() via GET
     * The json data we're testing:
     *
     *  {
     *      "status": "ok",
     *  }
     */
    public function test_status_GET ()
    {
        $this->setBaseURL("http://localhost/libraries/ResponseHound/Samples/server/jsonGetServer.php")
             ->addRequestParam("action", "status")
             ->send();

        /*
         * The most basic check. This will verify that the item is there and is populated. The
         * item must be at the root level.
         */
        $this->checkValue("status");

        /*
         * The second parameter defines the location in the tree to look. In this case, we're
         * at the root level, so nothing is needed.
         */
        $this->checkValue("status", "");

        /*
         * The third parameter allows us to enforce a type definition for the value. In this case,
         * we're specifying that it must be a string.
         */
        $this->checkValue("status", "", "string");

        /*
         * Last, we can specify any number of additional options to enforce on the item. Here we are
         * saying that "status" must be equila to "ok".
         */
        $this->checkValue("status", "", "string", array("value" => "ok"));
    }


    /**
     * Test colors->getList() via GET
     * The json data we're testing:
     *
     *  {
     *      "transactionId": 1,
     *      "status": "ok",
     *      "data":
     *      {
     *          "colors":
     *          [
     *              {"color": "red",     "value": "#f00"},
     *              {"color": "green",   "value": "#0f0"},
     *              {"color": "blue",    "value": "#00f"},
     *              {"color": "cyan",    "value": "#0ff"},
     *              {"color": "magenta", "value": "#f0f"},
     *              {"color": "yellow",  "value": "#ff0"},
     *              {"color": "black",   "value": "#000"}
     *          ]
     *      }
     *  }
     */
    public function test_getList_GET ()
    {
        $this->setBaseURL("http://localhost/libraries/ResponseHound/Samples/server/jsonGetServer.php")
             ->addRequestParam("action", "getList");

        /*
         * Note that $this->send() and $this->sendGet() are
         * aliases to $this->sendGetRequest().
         */
        $this->send();

        $this->checkValue("transactionId"  ,""        ,"int"    ,array("value" => 1))
             ->checkValue("status"         ,""        ,"string" ,array("value" => "ok"))
             ->checkValue("data"           ,""        ,"array")
             ->checkValue("colors"         ,"data"    ,"array");

        // Test a group of elements
        $loc = "data.colors";
        $this->checkListValue("color"    ,$loc      ,"string")
             ->checkListValue("value"    ,$loc      ,"string");


        /*
         * Test a group of elements using the option: valueList.
         * This allows the user to pass in a list of required values that
         * must be matched exactly, including the order.
         */
        $loc = "data.colors";
        $colors = array("red","green","blue","cyan","magenta","yellow","black");
        $hexValues = array("#f00","#0f0","#00f","#0ff","#f0f","#ff0","#000");
        $this->checkListValue("color"    ,$loc      ,"string" ,array("valueList" => $colors))
             ->checkListValue("value"    ,$loc      ,"string" ,array("valueList" => $hexValues));


        /*
         * Test a group of elements using the option: valueIn.
         * This allows the user to pass in a list of possible values.
         * The actual value must appear in the list, somewhere.
         */
        $loc = "data.colors";
        $colors = array("grey","red","gold","green","blue","white","cyan","magenta","yellow","black");
        $hexValues = array("#fff","#55d","#f00","#c33","#0f0","#00f","#0ff","#f0f","#ff0","#000");
        $this->checkListValue("color"    ,$loc      ,"string" ,array("valueIn" => $colors))
             ->checkListValue("value"    ,$loc      ,"string" ,array("valueIn" => $hexValues));
    }


    /**
     * Test colors->getList() via POST
     */
    public function test_getList_POST ()
    {
        $this->setBaseURL("http://localhost/libraries/ResponseHound/Samples/server/jsonPostServer.php")
             ->addRequestParam("action", "getList");

        /*
         * Note that $this->sendPost() is an alias to
         * $this->sendPostRequest().
         */
        $this->sendPost();

        $this->checkValue("transactionId"  ,""        ,"int"    ,array("value" => 1))
             ->checkValue("status"         ,""        ,"string" ,array("value" => "ok"))
             ->checkValue("data"           ,""        ,"array")
             ->checkValue("colors"         ,"data"    ,"array");

        // Test a group of elements
        $loc = "data.colors";
        $this->checkListValue("color"    ,$loc      ,"string")
             ->checkListValue("value"    ,$loc      ,"string");


        /*
         * Test a group of elements using the option: valueList.
         * This allows the user to pass in a list of required values that
         * must be matched exactly, including the order.
         */
        $loc = "data.colors";
        $colors = array("red","green","blue","cyan","magenta","yellow","black");
        $hexValues = array("#f00","#0f0","#00f","#0ff","#f0f","#ff0","#000");
        $this->checkListValue("color"    ,$loc      ,"string" ,array("valueList" => $colors))
             ->checkListValue("value"    ,$loc      ,"string" ,array("valueList" => $hexValues));


        /*
         * Test a group of elements using the option: valueIn.
         * This allows the user to pass in a list of possible values.
         * The actual value must appear in the list, somewhere.
         */
        $loc = "data.colors";
        $colors = array("grey","red","gold","green","blue","white","cyan","magenta","yellow","black");
        $hexValues = array("#fff","#55d","#f00","#c33","#0f0","#00f","#0ff","#f0f","#ff0","#000");
        $this->checkListValue("color"    ,$loc      ,"string" ,array("valueIn" => $colors))
             ->checkListValue("value"    ,$loc      ,"string" ,array("valueIn" => $hexValues));
    }
}