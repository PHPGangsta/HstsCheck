<?php
namespace PHPGangsta;

class HSTSChecker
{
    protected $_hostname = null;
    protected $_useHead = false;

    public function __construct($hostname = null)
    {
        $this->_hostname = $hostname;
    }

    public function setHostname($hostname)
    {
        $this->_hostname = $hostname;
    }

    public function getHSTSdetails()
    {
        $hstsHeader = $this->_getStrictTransportSecurityHeader();

        $hasHeader         = false;
        $includeSubdomains = null;
        $maxAge            = null;

        if ($hstsHeader !== null) {
            $hasHeader         = true;
            $includeSubdomains = false;

            $parts = explode(';', $hstsHeader);
            foreach ($parts as $part) {
                $keyValue = explode('=', $part, 2);
                if (count($keyValue) == 1) {
                    if (strtolower(trim($keyValue[0])) == 'includesubdomains') {
                        $includeSubdomains = true;
                    }
                } else {
                    if (strtolower(trim($keyValue[0])) == 'max-age') {
                        $maxAge = $keyValue[1];
                    }
                }
            }
        }

        return array(
            'hasHeader'         => $hasHeader,
            'maxAge'            => $maxAge,
            'includeSubdomains' => $includeSubdomains,
        );
    }

    protected function _getStrictTransportSecurityHeader()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_URL, 'https://'.$this->_hostname);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60); //timeout in seconds
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:25.0) Gecko/20100101 Firefox/25.0');

        if ($this->_useHead) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'HEAD'); // HTTP request is 'HEAD'
        }

        $response   = curl_exec($ch);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        curl_close($ch);

        if ($response === false) {
            throw new \Exception('No HTTPS');
        }

        $onlyHeaders     = substr($response, 0, $headerSize);
        $separateHeaders = explode("\r\n\r\n", trim($onlyHeaders));
        $lastHeader      = $separateHeaders[count($separateHeaders)-1];

        $headerLines = explode("\r\n", $lastHeader);
        foreach ($headerLines as $headerLine) {
            $parts = explode(':', $headerLine, 2);
            if (strtolower($parts[0]) == 'strict-transport-security') {
                return $parts[1];
            }
        }

        return null;
    }
}