<?php
session_start();
class Auth extends CI_Controller {

    private $data = array();

    function __construct() {
        parent::__construct();
        $this->load->model('modelAuth', 'model');
        $this->load->helper('url');

        $this->data['title'] = "Logowanie";
        $this->data['baseUrl'] = base_url();
    }

    public function index() {
        $isLogin = $this->isLogin();
        $this->view();
    }

    public function login() {
        $isLogin = $this->isLogin();
        if (!$isLogin) {
            $ret = $this->model->process();
            switch ($ret['status']) {
                case 'denied' :
                case 'old' :
                    $this->data['message'] = $ret['additional'];
                    break;
                case 'verified' :
                    redirect(base_url('index.php/radio'));
                    break;
                case 'redirect' :
                    redirect($ret['additional']);
                    break;
            }
        }
        $this->view();
    }

    public function logout() {
        session_destroy();
        $this->data['message'] = "Użytkownik zotał wylogowany";
        $this->view();
    }

    private function isLogin() {
        if (isset($_SESSION['status']) && $_SESSION['status'] == 'verified') {
            redirect(base_url('index.php/radio'));  
        }
        return false;
    }

    private function view() {
        $this->load->view('_standards/header', $this->data);
        $this->load->view('login/login', $this->data);
        $this->load->view('_standards/footer');
    }

}
