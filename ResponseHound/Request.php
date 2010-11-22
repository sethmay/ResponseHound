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

require_once "ResponseHound/Transport/Interface.php";
require_once "ResponseHound/Transport/Curl.php";
require_once "ResponseHound/Response/Interface.php";
require_once "ResponseHound/Response.php";
require_once "ResponseHound/Request/Interface.php";

/**
 * The common implemenation of the request interface. Used to
 * make requests against a web server using a URL.
 *
 * @category   Testing
 * @package    ResponseHound
 * @author     Seth May <seth@sethmay.net>
 * @copyright  2010 Seth May <seth@sethmay.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @since      Class available since Release 1.0
 */
class ResponseHound_Request implements ResponseHound_Request_Interface
{
    protected $url;
    protected $params;
    protected $response;
    protected $transport;



    /**
     * Constructs a request.
     *
     * @param  string $name
     * @param  array  $params
     */
    public function __construct (
         $url = null
        ,ResponseHound_Response_Interface $response = null
        ,array $params = array()
        ,ResponseHound_Transport_Interface $transport = null)
    {
        $this->setURL($url)
             ->setResponse($response)
             ->setParams($params)
             ->setTransport($transport);
    }


    /**
     * Add a parameter to the request. This will be passed along to
     * the server as part of the request.
     *
     * @param  string $name
     * @param  mixed $value
     * @return ResponseHound_Request_Interface
     */
    public function addParam ($name, $value)
    {
        $this->params[$name] = $value;
        return $this;
    }


    /**
     * Encode url param array
     *
     * @param array $var
     * @param string varName
     * @param string $seperator
     * @return string
     */
    protected function encodeArray ($var, $varName, $separator = '&')
    {
        $toImplode = array();
        foreach ($var as $key => $value)
        {
            if (is_array($value))
            {
                $toImplode[] = $this->encodeArray($value, "{$varName}[{$key}]",
                $separator);
            }
            else
            {
                $toImplode[] = "{$varName}[{$key}]=" . urlencode($value);
            }
        }
        return implode($separator, $toImplode);
    }



	/**
     * Retrieve a single parameter. If the requested hasn't been set,
     * a null will be returned
     *
     * @param string $name
     * @return mixed
     */
    public function getParam ($name)
    {
        $params = $this->getParams();

        if (array_key_exists($name, $params))
        {
            return $params[$name];
        }

        return null;
    }


    /**
     * Retrieve all set parameters.
     *
     * @return array
     */
    public function getParams ()
    {
        return $this->params;
    }


	/**
     * Retrieves the response object.
     *
     * @return ResponseHound_Response_Interface
     */
    public function getResponse ()
    {
        return $this->response;
    }


    /**
     * Retrieves the transport object.
     *
     * @return ResponseHound_Transport_Interface
     */
    public function getTransport ()
    {
        return $this->transport;
    }


    /**
     * Retrieve the set URL.
     *
     * @return string
     */
    public function getURL ()
    {
        return $this->url;
    }


    /**
     * Sends the request as a get
     *
     * @param string $url
     * @param array $params
     * @return array
     * @throw Exception
     */
    public function sendGet ($url = null, array $params = array())
    {
        return $this->sendRequest(self::SUBMIT_GET, $url, $params);
    }


    /**
     * Sends the request as a post.
     *
     * @param string $url
     * @param array $params
     * @return array
     * @throw Exception
     */
    public function sendPost ($url = null, array $params = array())
    {
        return $this->sendRequest(self::SUBMIT_POST, $url, $params);
    }


    /**
     * Sends the request.
     *
     * @param string $type
     * @param string $url
     * @param array $params
     * @return array
     * @throw Exception
     */
    public function sendRequest ($type = null, $url = null, array $params = array())
    {
        if ($type == null)
        {
            $type = self::SUBMIT_GET;
        }

        if ($url != null)
        {
            $this->setURL($url);
        }

        if (count($params) > 0)
        {
            foreach ($params as $name => $value)
            {
                $this->addParam($name, $value);
            }
        }

        try
        {
            if (!($this->getTransport() instanceof ResponseHound_Transport_Interface))
            {
                $this->setTransport(new ResponseHound_Transport_Curl());
            }

            $data = $this->getTransport()->getResponseData($this->getURL(), $type, $this->getParams());
        }
        catch (\Exception $e)
        {
            throw $e;
        }

        if (!($this->getResponse() instanceof ResponseHound_Response_Interface))
        {
            $this->setResponse(new ResponseHound_Response());
        }

        $this->getResponse()->setData($data);

        return $this->getResponse();
    }


	/**
     * Overwrite the full list of params with a suppled array.
     *
     * @param array $params
     * @return ResponseHound_Request_Interface
     */
    public function setParams (array $params = array())
    {
        $this->params = $params;
        return $this;
    }


	/**
     * Set the response object to be used.
     *
     * @param ResponseHound_Response_Interface $response
     * @return ResponseHound_Request_Interface
     */
    public function setResponse (ResponseHound_Response_Interface $response = null)
    {
        $this->response = $response;
        return $this;
    }


    /**
     * Set the transport object to be used.
     * 
     * @param ResponseHound_Transport_Interface $transport
     * @return ResponseHound_Request_Interface
     */
    public function setTransport (ResponseHound_Transport_Interface $transport = null)
    {
        $this->transport = $transport;
        return $this;
    }


    /**
     * Set the URL that will be called by this request.
     * 
     * @param string $url
     * @return ResponseHound_Request_Interface
     */
    public function setURL ($url = null)
    {
        $this->url = $url;
        return $this;
    }
}