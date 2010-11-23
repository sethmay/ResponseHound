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

/**
 * Implements a curl based transport.
 *
 * @category   Testing
 * @package    ResponseHound
 * @author     Seth May <seth@sethmay.net>
 * @copyright  2010 Seth May <seth@sethmay.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @since      Class available since Release 1.0
 */
class ResponseHound_Transport_Curl implements ResponseHound_Transport_Interface
{
    const SUBMIT_GET = "get";
    const SUBMIT_POST = "post";


    /**
     * Returns response data after sending the request
     *
     * @param string $type
     * @param string $url
     * @param array $params
     * @return string
     * @throws Exception
     */
    public function getResponseData ($url, $type = null, array $params = array())
    {
        if ($type == null)
        {
            $type = self::SUBMIT_GET;
        }
        try
        {
            // Open the curl request
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_VERBOSE, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            if ($type == self::SUBMIT_GET)
            {
                // Convert params to get syntax
                $paramList = '';

                $c=0;
                foreach ($params as $name => $value)
                {
                    if ($c > 0)
                    {
                        $paramList .= "&";
                    }

                    if (is_array($value))
                    {
                        $value = $this->encodeArray($value, $name);
                        $paramList .= $value;
                    }
                    else
                    {
                        $paramList .= $name."=".urlencode($value);
                    }
                    ++$c;
                }

                $url = $url . '?' . $paramList;

                curl_setopt($ch, CURLOPT_URL, $url);
            }
            else if ($type == self::SUBMIT_POST)
            {
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            }


            // Make the curl request and close
            $data = curl_exec($ch);

            if ($data === false)
            {
                throw new Exception ("Failed to connect to source: $url. The following error was thrown: ".curl_error($ch));
            }
            curl_close($ch);

        }
        catch (\Exception $e)
        {
            throw $e;
        }

        return $data;
    }


    /**
     * Encode an array as a GET query.
     *
     * @param array $args
     */
    protected function encodeArray(array $args = array())
    {
        if(!is_array($args))
        {
            return false;
        }

        $c = 0;
        $query = '';

        foreach($args as $name => $value)
        {
            if($c++ != 0)
            {
                $query .= '&';
            }

            $query .= urlencode("$name").'=';

            if(is_array($value))
            {
                $query .= urlencode(serialize($value));
            }
            else
            {
                $query .= urlencode("$value");
            }
        }
        return $query;
    }
}