<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Model do list radia
 */
class ModelLists extends CI_Model {
    
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
        return json_decode($list);
    }
	
    public function insertCurrent($list){
			foreach ($list as $r) {
                if ($r->order == 0) {
				$this->load->database();
				$query = $this->db->query('Select * from `muzyka` where (godz="'.$r->start.'" and autor="'.$r->author.'") and tytul="'.$r->title.'" ');
				if ($query->num_rows() == 0)
				{
				$query = $this->db->query('Insert INTO `muzyka` (godz, autor, tytul) VALUES ("'.$r->start.'", "'.$r->author.'", "'.$r->title.'")');	
				}
			  } 
			}
	}
	
	public function getStats(){
	 $this->load->database();
	 $query = $this->db->query('Select autor, count(id_muzyka) as liczba from `muzyka` group by autor');
	 echo 'Statystki zespołów: </br>';
	 foreach ($query->result_array() as $row)
		{
		echo $row['autor'];
		echo ' grany był: ';
		echo $row['liczba'];
		echo '</br>';
		}
		echo '</br>';
		
		echo 'Statystki utworów: </br>';
	$query = $this->db->query('Select tytul, count(id_muzyka) as liczba from `muzyka` group by tytul');
	 foreach ($query->result_array() as $row)
		{
		echo $row['tytul'];
		echo ' grany był: ';
		echo $row['liczba'];
		echo '</br>';
		}
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
            $nameId = $s->{'@attributes'}->idname;
            $url = site_url('station/'.$id.'/'.$nameId);
            $stations[$id] = array("name"=>$s->{'@attributes'}->name, "url"=>$url);
        }
        return $stations;
    }


}
