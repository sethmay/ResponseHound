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

require_once "data.php";


$target = "";
$action = "";
$transactionId = "";

if (isset($_GET["target"])) $target = $_GET["target"];
if (isset($_GET["action"])) $action = $_GET["action"];
if (isset($_GET["transactionId"])) $transactionId = $_GET["transactionId"];

if ($target == "colors" && $action == "getList")
{
    echo trim($colorListData);
    exit;
}

if ($target == "person" && $action == "getPerson")
{
    $id = null;
    if (isset($_GET["id"])) $id = $_GET["id"];

    if (!$id)
    {
        echo trim('
            {
                "status": "error",
                "message": "Id number needs to be supplied."
            }');
        exit;
    }

    if ($id != "1001")
    {
        echo trim('
            {
                "status": "error",
                "message": "Person not found."
            }');
        exit;
    }

    echo $personData;
    exit;
}


if ($target == "person" && $action == "getList")
{
    echo $personListData;
    exit;
}


echo trim('
{
    "status": "error",
}');
exit;
