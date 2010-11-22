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

/**
 * An interface that defines the contract that will be used by
 * all request objects.
 *
 * @category   Testing
 * @package    ResponseHound
 * @author     Seth May <seth@sethmay.net>
 * @copyright  2010 Seth May <seth@sethmay.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @since      Class available since Release 1.0
 */
interface ResponseHound_Request_Interface
{
    const SUBMIT_GET = "get";
    const SUBMIT_POST = "post";


    /**
     * Add a parameter to the request. This will be passed along to
     * the server as part of the request.
     * 
     * @abstract
     * @param  string $name
     * @param  mixed $value
     * @return ResponseHound_Request_Interface
     */
    function addParam ($name, $value);


    /**
     * Retrieve a parameter. If the requested hasn't been set,
     * a null will be returned
     *
     * @abstract
     * @param string $name
     * @return mixed
     */
    function getParam ($name);


    /**
     * Retrieve all set parameters.
     *
     * @abstract
     * @return array
     */
    function getParams ();


    /**
     * Retrieves the response object.
     *
     * @abstract
     * @return ResponseHound_Response_Interface
     */
    function getResponse ();


    /**
     * Retrieves the transport object.
     *
     * @abstract
     * @return ResponseHound_Transport_Interface
     */
    function getTransport ();


    /**
     * Retrieve the set URL.
     *
     * @abstract
     * @return string
     */
    function getURL ();


    /**
     * Sends the request as a get.
     *
     * @abstract
     * @param string $url
     * @param array $params
     * @return array
     * @throw Exception
     */
    function sendGet ($url = null, array $params = array());


    /**
     * Sends the request as a post.
     *
     * @abstract
     * @param string $url
     * @param array $params
     * @return array
     * @throw Exception
     */
    function sendPost ($url = null, array $params = array());


    /**
     * Sends the request.
     *
     * @abstract
     * @param string $type
     * @param string $url
     * @param array $params
     * @return array
     * @throw Exception
     */
    function sendRequest ($type = null, $url = null, array $params = array());


    /**
     * Overwrite the full list of params with a suppled array.
     *
     * @abstract
     * @param array $params
     * @return ResponseHound_Request_Interface
     */
    function setParams (array $params = array());


    /**
     * Set the response object to be used.
     *
     * @abstract
     * @param ResponseHound_Response_Interface $response
     * @return ResponseHound_Request_Interface
     */
    function setResponse (ResponseHound_Response_Interface $response = null);


    /**
     * Set the transport object to be used.
     *
     * @abstract
     * @param ResponseHound_Transport_Interface $transport
     * @return ResponseHound_Request_Interface
     */
    function setTransport (ResponseHound_Transport_Interface $transport = null);


    /**
     * Set the URL that will be called by this request.
     *
     * @abstract
     * @param string $url
     * @return ResponseHound_Request_Interface
     */
    function setURL ($url = null);
}
