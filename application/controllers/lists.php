<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Lists extends CI_Controller {

    private $data = array();
    private $defaultStationId = 5;

    public function __construct() {
        parent::__construct();
        $this->load->model('modelLists');
        
        $this->load->library('firephp');
        $this->load->helper('url');
        
        $this->data['title'] = "Lista";
        $this->data['baseUrl'] = base_url();
    }

    public function index() {
        $this->setStations();
        $this->setStationPlaylist($this->defaultStationId);
        $this->view();
        
    }
    
    public function station($id) {
        $this->setStations();
        $this->setStationPlaylist($id);
        $this->view();
        $this->firephp->log($_SERVER);
    }

    private function setStations() {
        $this->data['stations'] = $this->modelLists->getStations();
    }

    private function setStationPlaylist($id) {
        $list = $this->modelLists->getStationPlaylist($id);
        $this->data['playlist'] = $list;
        $this->firephp->log($list);
    }

    private function view() {
        $this->load->view('_standards/header', $this->data);
        $this->load->view('lists/list', $this->data);
        $this->load->view('_standards/footer');
    }

}
