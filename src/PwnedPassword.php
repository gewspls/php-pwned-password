<?php

namespace gewspls;

class PwnedPassword
{

    private $_Password;
    private $_Hash;
    private $_Head;
    private $_Tail;
    private $_Matches;

    protected $_Url = "https://api.pwnedpasswords.com/";

    public function __construct($password)
    {
        $this->_Password = (string)$password;
        $this->_Hash = $this->HashString();
        $this->_Head = $this->GetHashHead($this->_Hash);
        $this->_Tail = $this->GetHashTail($this->_Hash);
        $this->_Matches = array();
    }

    public function CheckPasswordExposure()
    {
        $possibleHashes = $this->ProcessPossibleHashes($this->MakeHttpRequest());
        return $this->SearchForMatches($possibleHashes);
    }

    public function GetExposureCount()
    {
        if(count($this->_Matches) > 0)
        {
            return $this->_Matches[0]['instanceCount'];
        }

        throw new Exception("No matches found to count");
    }

    private function HashString()
    {
        return sha1($this->_Password);
    }

    private function GetHashHead($hash)
    {
        return strtoupper(substr($hash, 0, 5));
    }

    private function GetHashTail($hash)
    {
        return strtoupper(substr($hash, 5, strlen($hash)));
    }

    private function GetUri()
    {
        return $this->_Url."/range/".$this->_Head;
    }

    private function MakeHttpRequest()
    {
        try
        {
            $curl = curl_init();
            curl_setopt_array($curl, $this->GetCurlOptions());
            $result = curl_exec($curl);
            curl_close($curl);

            if(!$result)
            {
                throw new Exception("Error making HTTP request: ".curl_error($curl)." Error code: ".curl_errno($curl));
            }

            return $result;
        }
        catch(Exception $ex)
        {
            throw new Exception("Unable to determine if password was exposed: ".$ex->getMessage());
        }
        
    }

    private function GetCurlOptions()
    {
        return array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $this->GetUri(),
            CURLOPT_CONNECTTIMEOUT => 60,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT']
        );
    }

    private function ProcessPossibleHashes($hashes)
    {
        $hashes = explode("\r\n", $hashes);

        if(!count($hashes) > 0)
        {
            throw new Exception("Unable to parse response.");
        }

        $possibleHashes = array();

        foreach($hashes as $hash)
        {
            $hashArray = explode(":", $hash);
            array_push(
                $possibleHashes,
                array(
                "hash" => $hashArray[0],
                "instanceCount" => $hashArray[1]
                )
            );
        }

        return $possibleHashes;
    }

    private function SearchForMatches($possibleHashes)
    {
        foreach($possibleHashes as $hash)
        {
            if($this->_Tail === $hash['hash'])
            {
                array_push($this->_Matches, $hash);
                return true;
            }
        }

        return false;
    }

}