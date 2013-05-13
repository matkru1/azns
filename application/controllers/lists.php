<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
session_start();
class Lists extends CI_Controller {

    private $data = array();
    private $defaultStationId = 5;

    public function __construct() {
        parent::__construct();
        $this->load->model('modelLists');

        $this->load->helper('url');

        $this->data['title'] = "Lista";
        $this->data['baseUrl'] = base_url();
        $this->isLogin();
    }

    public function radio() {
        $this->setStationsList();
        $this->setStationPlaylist($this->defaultStationId);
        $this->setStationNameId();
        $this->setStats();
        $this->view();

    }

    public function station($id, $stationId, $name) {
        $this->setStationsList();
        $this->setStationPlaylist($id);
        $this->setStationNameId($stationId, $name);
        $this->setStats();
        $this->view();
    }

    public function setStationsList() {
        $this->data['stations'] = $this->modelLists->getStations();
    }

    private function setStationPlaylist($id) {
        $list = $this->modelLists->getStationPlaylist($id);
        $this->data['playlist'] = $list;
        $this->modelLists->insertCurrent($list);
        $this->data['radioId'] = $id;
    }

    private function setStationNameId($nameId = "rmf", $name = "RMF FM") {
        $this->data['nameId'] = $nameId;
        $name = str_replace('__S__', ' ', $name);
        $this->data['name'] = $name;
    }

    private function setStats() {
        $stats = $this->modelLists->getStats();
        $this->data['stats'] = $stats;
    }

    private function isLogin() {
        if (!(isset($_SESSION['status']) && $_SESSION['status'] == 'verified')) {
            redirect(base_url());
        }
        $this->data['userName'] = $_SESSION['request_vars']['screen_name'];
    }

    private function view() {
        $this->load->view('_standards/header', $this->data);
        $this->load->view('lists/list', $this->data);
        $this->load->view('_standards/footer');
    }

    public function xajax_getPlaylist($id) {
        $list = $this->modelLists->getStationPlaylist($id);
        $this->modelLists->insertCurrent($list);
        die(json_encode($list));
    }

    public function xajax_getStats() {
        $stats = $this->modelLists->getStats();
        die(json_encode($stats));
    }

    public function generateXmlStats() {
        $this->modelLists->generateXmlStats();
        redirect(base_url('download/statystyka.xml'));
    }

}
