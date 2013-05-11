<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Lists extends CI_Controller {

    private $data = array();
    private $defaultStationId = 5;

    public function __construct() {
        parent::__construct();
        $this->load->model('modelLists');
        
        // $this->load->library('firephp');
        $this->load->helper('url');
        
        $this->data['title'] = "Lista";
        $this->data['baseUrl'] = base_url();
    }

    public function index() {
        $this->setStationsList();
        $this->setStationPlaylist($this->defaultStationId);
        $this->setStationNameId();
        $this->view();
        
    }
    
    public function station($id, $stationId) {
        $this->setStationsList();
        $this->setStationPlaylist($id);
        $this->setStationNameId($stationId);
        $this->view();
    }

    private function setStationsList() {
        $this->data['stations'] = $this->modelLists->getStations();
    }

    private function setStationPlaylist($id) {
        $list = $this->modelLists->getStationPlaylist($id);
        $this->data['playlist'] = $list;
		$this->modelLists->insertCurrent($list);
        $this->data['radioId'] = $id;
    }
    
    private function setStationNameId($name = "rmf") {
        $this->data['nameId'] = $name;
    }

    private function view() {
        $this->load->view('_standards/header', $this->data);
        $this->load->view('lists/list', $this->data);
        $this->load->view('_standards/footer');
    }
    
    public function xajax_getPlaylist($id) {
        $list = $this->modelLists->getStationPlaylist($id);
        die(json_encode($list));
    }

}
