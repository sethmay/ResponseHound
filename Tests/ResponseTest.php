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
require_once 'ResponseHound/Response.php';

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
class ResponseHound_ResponseTest extends PHPUnit_Framework_TestCase
{
    protected $testArrayData1;
    protected $testJsonData1;


    public function setUp ()
    {
        parent::setUp();

        $this->testArrayData1 = array("item1"=>1, "item2"=>"win");
        $this->testJsonData1 = json_encode($this->testArrayData1);
    }


    public function testConstruct ()
    {
        $actual = new ResponseHound_Response();
        $data = $this->readAttribute($actual, "data");
        $this->assertEmpty($data);

        $actual = new ResponseHound_Response($this->testJsonData1);
        $data = $actual->getData();
        $this->assertArrayHasKey("item1", $data);
        $this->assertArrayHasKey("item2", $data);

        $this->assertEquals(1, $data["item1"]);
        $this->assertEquals("win", $data["item2"]);

    }


    public function testGetData ()
    {
        $actual = new ResponseHound_Response();
        $this->assertEmpty($actual->getData());

        $actual = new ResponseHound_Response($this->testJsonData1);
        $data = $actual->getData();
        $this->assertArrayHasKey("item1", $data);
        $this->assertArrayHasKey("item2", $data);

        $this->assertEquals(1, $data["item1"]);
        $this->assertEquals("win", $data["item2"]);
    }


    public function testSetData ()
    {
        $actual = new ResponseHound_Response();
        $actual->setData($this->testArrayData1);
        $data = $this->readAttribute($actual, "data");

        $this->assertArrayHasKey("item1", $data);
        $this->assertArrayHasKey("item2", $data);
        $this->assertEquals(1, $data["item1"]);
        $this->assertEquals("win", $data["item2"]);


        $actual->setData($this->testJsonData1);
        $data = $this->readAttribute($actual, "data");
        $this->assertArrayHasKey("item1", $data);
        $this->assertArrayHasKey("item2", $data);

        $this->assertEquals(1, $data["item1"]);
        $this->assertEquals("win", $data["item2"]);


        $this->setExpectedException('InvalidArgumentException');
        $actual->setData("foo");
    }
}
?>
