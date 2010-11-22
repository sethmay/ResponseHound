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

require_once "ResponseHound/Response/Interface.php";

/**
 * A simple response object. Will take data (generally return from a
 * request call) and parses it into usable data. In this case, it
 * is often JSON string data that is put into array form.
 *
 * @category   Testing
 * @package    ResponseHound
 * @author     Seth May <seth@sethmay.net>
 * @copyright  2010 Seth May <seth@sethmay.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @since      Class available since Release 1.0
 */
class ResponseHound_Response implements ResponseHound_Response_Interface
{
    /**
     * The JSON response data in array form.
     * @var array
     */
    protected $data;


    
    /**
     * Constructs a request.
     *
     * @param  string $data
     */
    public function __construct($data = null)
    {
        $this->setData($data);
    }


    /**
     * Returns response data.
     *
     * @return array
     */
    public function getData()
    {
       return $this->data;
    }



    /**
     * Parse an incoming string as JSON. Create a new
     * array out of it.
     *
     * @param string $json
     * @return ResponseHound_Response_Interface
     */
    protected function parseJSONString ($json)
    {
        $result = json_decode($json, true);

        if (!is_array($result))
        {
            throw new InvalidArgumentException("Arguement must be a valid JSON string. The following was recieved: ".$json);
        }

        return $result;
    }


    /**
     * Sets the response data. This can be an array or a valid
     * JSON string.
     *
     * @param mixed $data
     * @return ResponseHound_Response_Interface
     */
    public function setData($data = null)
    {
        if ($data == null)
        {
            $this->data = array();
        }
        else if (is_array($data))
        {
            $this->data = $data;
        }
        else if (is_string($data))
        {
            try
            {
                $this->data = $this->parseJSONString($data);
            }
            catch(InvalidArgumentException $e)
            {
                throw $e;
            }
        }
        else
        {
            throw new InvalidArgumentException("Arguement must be either an array or JSON string. The following was recieved: ".$data);
        }

        return $this;
    }
}