<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class ModelLists extends CI_Model {

    private $CONSUMER_KEY = 'OyplEQjLvJ66a2S1y7gfyQ';
    private $CONSUMER_SECRET = 'zHQ91nyWDctWfb198k0z0KSP4mOwT5yKWqrNej0oaGU';
    private $OAUTH_CALLBACK = 'http://localhost/radio/index.php/login';

    public function __construct() {
        $this->load->helper('url');
    }

    public function curl_download($Url) {
        if (!function_exists('curl_init')) {
            die('Sorry cURL is not installed!');
        }
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $Url);
        curl_setopt($ch, CURLOPT_REFERER, "http://www.rmfon.pl/");
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.2; WOW64; rv:20.0) Gecko/20100101 Firefox/20.0");
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $output = curl_exec($ch);
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

    public function getStats() {
        $this->load->database();
        $userId = $_SESSION['request_vars']['user_id'];
        $sql = 'SELECT autor, count( id_muzyka ) AS liczba ' 
            . ' FROM `muzyka` ' 
            . ' WHERE userId = ' . $userId . ' '
            . ' GROUP BY autor ' . ' ORDER BY liczba DESC ' . ' LIMIT 5 ';
        $query = $this->db->query($sql);
        $topAutors = $query->result_array();
        $sql = 'SELECT autor, tytul, count(id_muzyka) AS liczba ' 
        . ' FROM `muzyka` '
        . ' WHERE userId = ' . $userId . ' ' 
        . ' GROUP BY autor, tytul ' 
        . ' ORDER BY liczba DESC ' . ' LIMIT 5 ';
        $query = $this->db->query($sql);
        $topTitles = $query->result_array();
        $top = array(
            "autors" => $topAutors,
            "titles" => $topTitles
        );
        return $top;
    }

    public function getStations() {
        $url = "http://www.rmfon.pl/xml/stations.txt";
        $xmlPlain = $this->curl_download($url);
        $stations = $this->parseStations($xmlPlain);
        // echo '<pre>';
        // print_r($stations);
        // echo '</pre>';
        // die();
        return $stations;
    }

    public function parseStations($xmlStations) {
        $xml = simplexml_load_string($xmlStations);
        $xml = json_decode(json_encode($xml));
        $stations = array();
        foreach ($xml->station as $s) {
            $id = $s->{'@attributes'}->id;
            $nameId = $s->{'@attributes'}->idname;
            $name = $s->{'@attributes'}->name;
            $urlName = str_replace(' ', '__S__', $name);
            $url = site_url('station/' . $id . '/' . $nameId. '/' . $urlName);
            $stations[$id] = array(
                "name" => $name,
                "url" => $url
            );
        }
        return $stations;
    }

    public function insertCurrent($list) {
        foreach ($list as $r) {
            if ($r->order == 0) {
                $this->load->database();
                $songdate = date('Y-m-d H:i:s', $r->timestamp);
                $author = trim(addslashes($r->author));
                $title = trim(addslashes($r->title));
                $userId = $_SESSION['request_vars']['user_id'];
                $sql = 'Select * from `muzyka` ' . ' where godz="' . $songdate . '" and autor="' . $author . '" and tytul="' . $title . '" and userId = ' . $userId;
                $query = $this->db->query($sql);
                if ($query->num_rows() == 0) {
                    $createdate = date('Y-m-d H:i:s');
                    $sql = 'Insert INTO `muzyka` (godz, autor, tytul, userId, create_date) ' . ' VALUES ("' . $songdate . '", "' . $author . '", "' . $title . '", ' . $userId . ', "' . $createdate . '")';
                    $query = $this->db->query($sql);
                    $this->sendTweet($author, $title);
                }
            }
        }
    }

    private function sendTweet($author, $title) {
        $song = $author . (!empty($title) ? ' - ' . $title : '');
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
    
    public function generateXmlStats() {
        $this->load->library('arraytoxml', '', 'xml');
        $data= $this->getStats();
        $this->xml->toXml($data, 'muzyka');
    }

}
