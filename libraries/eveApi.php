<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2016 Robert Sardinia, ChrisWF
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

/**
 * @param $url
 * @return mixed|null
 */
function makeApiRequest($url)
{
    // Initialize a new request for this URL
    $ch = curl_init($url);
    // Set the options for this request
    curl_setopt_array($ch, array(
        CURLOPT_FOLLOWLOCATION => true, // Yes, we want to follow a redirect
        CURLOPT_RETURNTRANSFER => true, // Yes, we want that curl_exec returns the fetched data
        CURLOPT_SSL_VERIFYPEER => false, // Do not verify the SSL certificate
        CURLOPT_TIMEOUT => 15,
    ));
    // Fetch the data from the URL
    $data = curl_exec($ch);
    // Close the connection
    curl_close($ch);
    // Return a new SimpleXMLElement based upon the received data
    return new SimpleXMLElement($data);
}

/**
 * @param $url
 * @return mixed|null
 */
function makeCrestRequest($path, $token=false, $method="GET", $requestData=false)
{
    $url = "https://crest-tq.eveonline.com" . $path;
    // Initialize a new request for this URL
    $ch = curl_init($url);
    // Set the options for this request
    curl_setopt_array($ch, array(
        CURLOPT_FOLLOWLOCATION => true, // Yes, we want to follow a redirect
        CURLOPT_RETURNTRANSFER => true, // Yes, we want that curl_exec returns the fetched data
        CURLOPT_SSL_VERIFYPEER => false, // Do not verify the SSL certificate
        CURLOPT_TIMEOUT => 15,
        CURLOPT_USERAGENT => "Eve-Fleet-Linkup (eve_crest@chriswf.de)"
    ));     
    if($method != "GET")
       curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    if($requestData != false)
       curl_setopt($ch, CURLOPT_POSTFIELDS, $requestData);
        
    $headerData = [];  
    if($token != false)
       $headerData[] = "Authorization: Bearer $token";
    if($method != "GET")
       $headerData[] = "Content-Type: */*";
    $headerData[] = "Accept: */*";
    if(count($headerData) > 0)
       curl_setopt($ch, CURLOPT_HTTPHEADER, $headerData);    
    
    // Fetch the data from the URL
    $data = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    // Close the connection
    curl_close($ch);
    if($httpcode >= 400)
    {    
        error_log("HTTP code $httpcode for request to $url");
        error_log("Path: $path, Token: $token, Method: $method, Request Data: $requestData");
        error_log("Response Data: $data");
    } else {
    }
     // Return a new object based upon the received data
     return json_decode($data);
}

/**
 * @return mixed|null
 
 */

function serverStatus(){
    // Initialize a new request for this URL
    $ch = curl_init("https://api.eveonline.com/server/ServerStatus.xml.aspx");
    // Set the options for this request
    curl_setopt_array($ch, array(
        CURLOPT_FOLLOWLOCATION => true, // Yes, we want to follow a redirect
        CURLOPT_RETURNTRANSFER => true, // Yes, we want that curl_exec returns the fetched data
        CURLOPT_TIMEOUT => 8,
        CURLOPT_SSL_VERIFYPEER => false, // Do not verify the SSL certificate
    ));
    // Fetch the data from the URL
    $data = curl_exec($ch);
    // Close the connection
    curl_close($ch);

    $true = "true";
    //If server is down return false
    if ($data->serverOpen != "True") {
        return FALSE;
    }
    //If server is up return true
    return $true;
}
