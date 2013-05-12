<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Model do list radia
 */
class ModelLists extends CI_Model {

    private $CONSUMER_KEY = 'OyplEQjLvJ66a2S1y7gfyQ';
    private $CONSUMER_SECRET = 'zHQ91nyWDctWfb198k0z0KSP4mOwT5yKWqrNej0oaGU';
    private $OAUTH_CALLBACK = 'http://localhost/laska/index.php/login';

    public function __construct() {
        // $this->load->library('firephp');
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
        $list = json_decode($list);
        foreach ($list as &$r) {
            $r->stime = date('H:i', $r->timestamp);
        }
        unset($r);
        return $list;
    }

    public function insertCurrent($list) {
        foreach ($list as $r) {
            if ($r->order == 0) {
                $this->load->database();
                $query = $this->db->query('Select * from `muzyka` where (godz="' . $r->start . '" and autor="' . $r->author . '") and tytul="' . $r->title . '" ');
                if ($query->num_rows() == 0) {
                    $query = $this->db->query('Insert INTO `muzyka` (godz, autor, tytul) VALUES ("' . $r->start . '", "' . $r->author . '", "' . $r->title . '")');
                    $this->sendTweet($r->author, $r->title);
                }
            }
        }
    }

    private function sendTweet($author, $title) {
        $song = $author . (!empty($title) ? ' - ' . $title : '' ); 
        $msg = "Aktualnie słucham: $song";
        $twitterParam = array(
            "consumer_key" => $this->CONSUMER_KEY,
            "consumer_secret" => $this->CONSUMER_SECRET,
            "oauth_token" => $_SESSION['request_vars']['oauth_token'],
            "oauth_token_secret" => $_SESSION['request_vars']['oauth_token_secret']
        );
        $this->load->library('twitter/twitteroauth', $twitterParam, 'twitter');
        $this->twitter->post('statuses/update', array('status' => $msg));
    }

    public function getStats() {
        $this->load->database();
        $query = $this->db->query('Select autor, count(id_muzyka) as liczba from `muzyka` group by autor');
        echo 'Statystki zespołów: </br>';
        foreach ($query->result_array() as $row) {
            echo $row['autor'];
            echo ' grany był: ';
            echo $row['liczba'];
            echo '</br>';
        }
        echo '</br>';

        echo 'Statystki utworów: </br>';
        $query = $this->db->query('Select tytul, count(id_muzyka) as liczba from `muzyka` group by autor, tytul');
        foreach ($query->result_array() as $row) {
            echo $row['tytul'];
            echo ' grany był: ';
            echo $row['liczba'];
            echo '</br>';
        }
    }

    public function sqlToXml($queryResult, $rootElementName, $childElementName) {
        $xmlData = "<?xml version='1.0' encoding='utf-8' ?>n";
        $xmlData .= "<" . $rootElementName . ">";

        while ($record = mysql_fetch_object($queryResult)) {
            $xmlData .= "<" . $childElementName . ">";

            for ($i = 0; $i < mysql_num_fields($queryResult); $i++) {
                $fieldName = mysql_field_name($queryResult, $i);

                /* Pobieranie nazwy kolumny */
                $xmlData .= "<" . $fieldName . ">";
                if (!empty($record->$fieldName))
                    $xmlData .= $record->$fieldName;
                else
                    $xmlData .= "null";

                $xmlData .= "</" . $fieldName . ">";
            }
            $xmlData .= "</" . $childElementName . ">";
        }
        $xmlData .= "</" . $rootElementName . ">";

        return $xmlData;
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
        foreach ($xml->station as $s) {
            $id = $s->{'@attributes'}->id;
            $nameId = $s->{'@attributes'}->idname;
            $url = site_url('station/' . $id . '/' . $nameId);
            $stations[$id] = array(
                "name" => $s->{'@attributes'}->name,
                "url" => $url
            );
        }
        return $stations;
    }

}
