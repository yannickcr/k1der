<?php
/**
 * Wrapper for Webthumb.
 *
 * Wrapper for the WebThumb API by Joshua Eichorn
 * http://bluga.net/webthumb/
 *
 * Currently this requires that allow_url_fopen be set to true to retrieve the
 * image. The next version won't.
 *
 * @package Webthumb
 * @author Cal Evans <cal@zend.com>
 * @copyright 2006 Cal Evans
 * @license GLPv2
 * @version 1.1
 * @example usage.php
 * @todo Implement calls to jobs we did not initiate.
 * @todo Implement Job class
 *
 * Version History:
 * 1.0 - 09/28/2006 - Cal
 * Initial Release
 *
 * 1.1 - 10/01/2006 - Cal
 * re-implemented fetchToFile to work with the fetch image protocol. This
 * eliminates the need for allow_utl_fopen to be set to true.
 */

class Eicc_Webthumb {
    /**
     * 2 dimensional array of urls to image.
     *
     * @var array
     */
    public $urlsToImage;

    /**
     * the domain of the webthumb api.
     *
     * @var string
     */
    protected $webthumbDomain = 'webthumb.bluga.net';

    /**
     * the base URL to download the images from.
     *
     * @var string
     */
    protected $imageUrl = 'http://webthumb.bluga.net/data/';

    /**
     * the script to call for webthumb
     *
     * @var string
     */
    protected $webthumbScript = '/api.php';


    /**
     * array of XML snippets to assemble into a call.
     *
     * @var unknown_type
     */
    protected $xml;

    /**
     * the webthumb api key.
     *
     * @var unknown_type
     */
    protected $apiKey;

    /**
     * the base dir to place the files in.
     *
     * @var unknown_type
     */
    protected $baseDir = './';


    /**
     * constructor
     *
     */
    function __construct()
    {
        $this->response       = array();
        $this->urlsToImage    = array();
        $this->xml            = array();
        $this->xml['wrapper'] = '<webthumb><apikey>%s</apikey>%s</webthumb>';
        $this->xml['request'] = '<request><url>%s</url><width>%d</width><height>%d</height></request>';
        $this->xml['status']  = '<status><job>%s</job></status>';
        $this->xml['fetch']   = '<fetch><job>%s</job><size>%s</size></fetch>';


    } // function __construct()


    /**
     * set the webthumb api key.
     *
     * @param unknown_type $newKey
     */
    public function setApiKey($newKey)
    {
        if (is_string($newKey)) {
            $this->apiKey = $newKey;
        } // if (is_string($newKey))
    } // public function setApiKey($newKey)


    /**
     * add a url to the stack and set the properties for the request.
     *
     * @param String  $url
     * @param string  $size
     * @param Integer $width
     * @param Integer $height
     * @return bool
     */
    public function addUrl($url, $size='small', $width=300, $height=300)
    {
        $returnValue = false;

        if (is_string($url) ) {

            $this->urlsToImage[$url] = array('size'       => $size,
                                             'width'      => $width,
                                             'height'     => $height,
                                             'job'        =>'',
                                             'est_time'   => '',
                                             'pickup_url' => '',
                                             'status'     =>'New');
            $returnValue =true;
        } // if (is_string(($url) and is_int($width) and is_int($height)))
        return $returnValue;
    } // public function addUrl($url, $size='small',$width=300, $height=300)


    /**
     * delete a url from the stack
     *
     * @param String $url
     * @return boolean
     */
    public function deleteUrl($url)
    {
        $returnValue = false;
        if (array_key_exists($url, $this->urlsToImage)) {
            unset($this->urlsToImage[$url]);
            $returnValue = true;
        } // if (array_key_exists($url, $this->urlsToImage))
        return $returnValue;
    } // public function deleteUrl($url)


    /**
     * submit the queued URLS to webthumb.
     * @throws Exception
     *
     */
    public function submitRequests()
    {

        if (count($this->urlsToImage)<1) {
            throw new Exception('No URLSs to image');
        } // if (count($this->urlsToImage)<1)

        $xml = $this->_buildSubmitRequestXml();

        if (strlen($xml)<1) {
            throw new Exception('All queued URLs have already been submitted');
        } // if (strlen($xml)<1)

        $payload  = $this->_preparePayload($xml);
        $response = $this->_transmitRequest($payload);

        $this->_parseSubmitResponse($response);
        return;
    } // public function submitRequests()


    /**
     * prepares and sends the check status payload for all submitted jobs.
     *
     */
    public function checkJobStatus()
    {
        if (count($this->urlsToImage)<1) {
            throw new Exception('No jobs to check');
        } // if (count($this->urlsToImage)<1)

        $xml = $this->_buildStatusRequestXml();

        if (strlen($xml)>0) {
            $payload  = $this->_preparePayload($xml);
            $response = $this->_transmitRequest($payload);
            $this->_parseStatusResponse($response);
        } // if (strlen($xml)<1)
        return;
    } // public function checkJobStatus()


    /**
     * Builds the XML payload to send to the webthumb API to queue the
     * requested images
     *
     * @return String
     */
    protected function _buildSubmitRequestXml()
    {
        $requestWork = '';
        $finalXml    = '';

        foreach($this->urlsToImage as $url=>$params) {

            // don't queue anything that has already been queued.
            if ($params['status']==="New") {
                $requestWork .= sprintf($this->xml['request'],
                                        (String)$url,
                                        (Integer)$params['width'],
                                        (Integer)$params['height']);
            } // if (is_null($params['est_time']))
        } // if ($params['status']==="New")

        $finalXml = sprintf($this->xml['wrapper'],
                            $this->apiKey,
                            $requestWork);

        return $finalXml;
    } // protected function _buildSubmitRequestXml()


    /**
     * Builds the XML payload to request the job status on any job that has
     * been queued and has passed it's estimated time.
     *
     * @return String
     */
    protected function _buildStatusRequestXml()
    {
        $requestWork = '';
        $finalXml    = '';

        foreach($this->urlsToImage as $params) {
            if ($params['est_time']<time()) {
                $requestWork .= sprintf($this->xml['status'], $params['job']);
            } // if ($params['est_time']<time())
        } // foreach($this->urls_to_thumbnail as $url=>$size)

        if (strlen($requestWork)>0) {
            $finalXml = sprintf($this->xml['wrapper'],
                                $this->apiKey,
                                $requestWork);
        } // if (strlen($requestWork)>0)

        return $finalXml;
    } // protected function  _buildStatusRequestXml()


    /**
     * build the XML payload for fetching an image
     *
     * @param string $job
     * @return string
     */
     protected function _buildFetchRequestXml($job) {
        $requestWork = '';
        $finalXml    = '';
        $thisURL     = $this->_findUrlByJobId($job);

        if ($this->urlsToImage[$thisURL]['status']==='Complete') {
            $requestWork .= sprintf($this->xml['fetch'], $this->urlsToImage[$thisURL]['job'],$this->urlsToImage[$thisURL]['size']);
        } // if ($params['status']==='Complete')

        $finalXml = sprintf($this->xml['wrapper'],
                             $this->apiKey,
                             $requestWork);

        return $finalXml;
     } // protected function _buildFetchRequestXml()


    /**
     * handles the actual transmission of the payload.
     *
     * @param unknown_type $request
     * @return String the response from the server.
     */
    protected function _transmitRequest($request)
    {
        $response = '';
        $errno    = 0;
        $errstr   = '';
        $handle   = fsockopen($this->webthumbDomain, 80, $errno, $errstr, 30);
        stream_set_timeout($handle,10);

        if (!$handle) {
            throw new Exception('Error opening the socket');
        } //if (!$handle)

        fwrite($handle, $request);

        while (!feof($handle)) {
            $response .= fgets($handle, 128);
        } // while (!feof($handle))

        fclose($handle);

        return $response;

    } // protected function _transmitRequest($request)


    /**
     * Takes the response from submitting urls for imaging and parses it out.
     * Places the responses in the appropriate places in the urlsToImage
     * array.
     *
     * @param string $response the response to process
     *
     */
    protected function _parseSubmitResponse($response)
    {
        $this->_checkContentType($response);

        if ( strpos($response, '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">')!==false) {
            $filteredResponse = trim(substr($response, 0, strpos($response, '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">')));
        } else {
            $filteredResponse = $response;
        } // if ( strpos($response, '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">')!==false)

        $responseArray = explode(chr(13).chr(10).chr(13).chr(10), $filteredResponse);

        /*
        * 0==http headers
        * 1==payload
        */
        $xml = new SimpleXMLElement($responseArray[1]);

        foreach ($xml->jobs->job as $thisJob) {
            $thisUrl = (String)$thisJob['url'];
            $this->urlsToImage[$thisUrl]['job']      = (String)$thisJob;
            $this->urlsToImage[$thisUrl]['est_time'] = strtotime((String)$thisJob['time']);
            $this->urlsToImage[$thisUrl]['status']   = 'Transmitted';
        } // foreach ($xml->jobs as $thisJob)

        return;
    } // protected function _parseSubmitResponse($response)


    /**
     * Takes the response from a status check request and parses it out. Places
     * the relevant information in the urlsToImage array.
     *
     * @param String $response the response to process.
     *
     */
    protected function _parseStatusResponse($response)
    {
        // this throws an exception
        $this->_checkContentType($response);

        if ( strpos($response, '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">')!==false) {
            $filteredResponse = trim(substr($response, 0, strpos($response, '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">')));
        } else {
            $filteredResponse = $response;
        } // if ( strpos($response, '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">')!==false)

        $responseArray    = explode(chr(13).chr(10).chr(13).chr(10), $filteredResponse);

        $xml = new SimpleXMLElement($responseArray[1]);

        foreach ($xml->jobStatus->status as $thisJob) {
            $thisId      = (String)$thisJob['id'];
            $thisUrl     = $this->_findUrlByJobId($thisId);

            $this->urlsToImage[$thisUrl]['status']     = (String)$thisJob;
            $this->urlsToImage[$thisUrl]['pickup_url'] = (String)$thisJob->status['pickup'];
        } // foreach ($xml->jobs as $thisJob)

        return;
    } // protected function _parseStatusResponse()


    /**
     * finds the Content-Type in the response.
     *   XML  == Good
     *   HTML == Error.
     *
     * @param string $response
     * @return boolean
     * @throws Exception
     */
    protected function _checkContentType($response)
    {
        $matches = array();
        preg_match('/Content-Type:(.*)/', $response, $matches);

        if (count($matches)>=2) {
            $contentType = trim($matches[1]);
        } else {
            throw new Exception('No Content-Type in response.');
        } // if ($count($matches)>=2)

        if (($contentType!='text/xml')) {
            throw new Exception('There was an error. Content-Type returned was '.$contentType);
        } // if (($contentType!='text/xml'))

        return true;
    } // protected function _checkContentType($response)


    /**
     * Given a jobID, this finds the URL for it. Used in the status parser to
     * match up the information to the proper array.
     *
     * @param string $jobId
     * @return string The URL found.
     */
    protected function _findUrlByJobId($jobId)
    {
        $thisUrl='';
        foreach($this->urlsToImage as $url=>$params) {
            if ($params['job']===$jobId) {
                $thisUrl = $url;
                break;
            } // if ($params['job']===$job_id)
        } // foreach($this->urlsToImage as $url=>$params)
        return $thisUrl;
    }


    /**
     * check to see if all images are ready for download.
     *
     * @return boolean
     */
    public function readyToDownload()
    {
        $returnValue = true;
        foreach($this->urlsToImage as $params) {
            $returnValue = ($returnValue AND $params['status']==='Complete');
        } // foreach($this->urlsToImage as $url=>$params)
        return $returnValue;
    } // public function readyToDownload()


    /**
     * If all images are complete then it will send a request for each image.
     *
     */
    public function fetchAll()
    {

        if (count($this->urlsToImage)<1) {
            throw new Exception('No URLSs to image');
        } // if (count($this->urlsToImage)<1)

        if (!$this->readyToDownload()) {
            throw new Exception('No images ready to download.');
        } // if (!$this->readyToDownload())

        foreach ($this->urlsToImage as $params) {
            if($this->urlsToImage['status'==='Complete']) {
                $this->fetchToFile($params['job'],$params['size']);
            } // if($this->urlsToImage['status'==='Complete'])
        } // foreach ($this->urlsToImage as $params)

        return;
    } // public function fetchAll()


    /**
     * fetches the given jobid and stores it on the filesystem in the filename
     * specified.
     *
     * @param string $job
     * @param string $filename
     * @param string $size
     *
     */
    public function fetchToFile($job='', $filename='', $size='small' )
    {
        if (empty($job)) {
            throw new Exception('No job id specified');
        } // if (empty($filename))

        if (empty($filename)) {
            $filename = $job.'.jpg';
        } // if (empty($filename))

        if ($size != 'small' and
            $size != 'medium' and
            $size != 'medium2' and
            $size != 'large') {
            throw new Exception($size . ' is an invalid value for the size parameter');
        } // if (empty($filename))

        $xml     = $this->_buildFetchRequestXml($job);
        $payload = $this->_preparePayload($xml);

        $image   = $this->_transmitRequest($payload);
        $image=substr($image,strpos($image,chr(10).chr(13))+3);
        file_put_contents($filename, $image);
        return;
    } // public function fetchToFile($job='', $filename='', $size='small' )


    /**
     * sets the baseDir
     *
     * @param string $newValue
     * @return boolean
     */
    public function setBaseDir($newValue='')
    {
        $returnValue = false;

        if (!empty($newValue)) {
            $newValue = trim($newValue);
            $newValue .= substr($newValue, -1)!='/'?'/':'';
            $this->baseDir = $newValue;
        } // if (!empty($newValue))

        return $returnValue;
    } // public function setBaseDir($newValue='')


    /**
     * retrieves the baseDir
     *
     * @return string
     */
    public function getBaseDir()
    {
        return $this->baseDir;
    } // public function getBaseDir()


    /**
     * prepares the HTTP Post with XML Payload.
     *
     * @param String $xml
     * @return String
     */
    protected function _preparePayload($xml)
    {
        $httpRequest  = "POST ".$this->webthumbScript." HTTP/1.1\r\n";
        $httpRequest .= "Host: ".$this->webthumbDomain."\r\n";
        $httpRequest .= "Content-Type: text/xml\r\n";
        $httpRequest .= "Connection: Close\r\n";
        $httpRequest .= "Content-Length:".strlen($xml)."\r\n\r\n";
        $httpRequest .= $xml."\r\n";

        return $httpRequest;
    } // protected function _preparePayload($xml)


}// class Eicc_Webthumb