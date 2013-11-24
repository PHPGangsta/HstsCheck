<?php
namespace PHPGangsta;

class AsyncRunner extends \Stackable
{
    protected $_hostname;
    protected $_rank;
    protected $_data;

    public function __construct($hostname, $rank)
    {
        $this->_hostname = $hostname;
        $this->_rank     = $rank;
    }

    public function getData()
    {
        return $this->_data;
    }

    public function run()
    {
        $hstsChecker = new HSTSChecker($this->_hostname);

        try {
            $results = $hstsChecker->getHSTSdetails();

            $this->_data = array($this->_rank, $this->_hostname, true, $results['hasHeader'], $results['maxAge'], $results['includeSubdomains']);
        } catch (\Exception $e) {
            $this->_data = array($this->_rank, $this->_hostname, false, null, null, null);
        }

        echo '.';
    }
}