<?php

/**
 * Model do list radia
 */
class ModelLists extends CI_Model {
    
    public function __construct() {
        $this->load->library('firephp');
        $this->load->helper('url');
    }

    public function curl_download($Url) {
        // is cURL installed yet?
        if (!function_exists('curl_init')) {
            die('Sorry cURL is not installed!');
        }
        // OK cool - then let's create a new cURL resource handle
        $ch = curl_init();

        // Now set some options (most are optional)

        // Set URL to download
        curl_setopt($ch, CURLOPT_URL, $Url);
        // Set a referer
        curl_setopt($ch, CURLOPT_REFERER, "http://www.example.org/yay.htm");
        // User agent
        curl_setopt($ch, CURLOPT_USERAGENT, "MozillaXYZ/1.0");
        // Include header in result? (0 = yes, 1 = no)
        curl_setopt($ch, CURLOPT_HEADER, 0);
        // Should cURL return or print out the data? (true = return, false = print)
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Timeout in seconds
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        // Download the given URL, and return output
        $output = curl_exec($ch);

        // Close the cURL resource, and free system resources
        curl_close($ch);

        return $output;
    }

    public function getStationPlaylist($id) {
        $time = time();
        $url = "http://www.rmfon.pl/stacje/playlista_$id.json.txt?t=$time";
        $list = $this->curl_download($url);
        return json_decode($list);
    }
    
    public function getStations() {
        $url = "http://www.rmfon.pl/xml/stations.txt";
        $xmlPlain = $this->curl_download($url);
        $stations = $this->parseStations($xmlPlain);
        return $stations;
    }
    
    public function parseStations($xmlStations) {
        $xml = simplexml_load_string($xmlStations);
        $xml = json_decode(json_encode($xml));
        $stations = array();
        foreach($xml->station as $s) {
            $id = $s->{'@attributes'}->id;
            $url = site_url('station/'.$id);
            $stations[$id] = array("name"=>$s->{'@attributes'}->name, "url"=>$url);
        }
        return $stations;
    }


}