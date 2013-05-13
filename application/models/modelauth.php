<?php
class ModelAuth extends CI_Model {

    private $CONSUMER_KEY = 'OyplEQjLvJ66a2S1y7gfyQ';
    private $CONSUMER_SECRET = 'zHQ91nyWDctWfb198k0z0KSP4mOwT5yKWqrNej0oaGU';
    private $OAUTH_CALLBACK = 'http://localhost/radio/index.php/login';

    function __construct() {
        $this->load->helper('url');
    }

    public function process() {
        $status = '';
        $additional = '';

        if (isset($_REQUEST['oauth_token']) && $_SESSION['token'] !== $_REQUEST['oauth_token']) {
            session_destroy();
            $status = 'old';
            $additional = 'Spróbuj ponownie!';

        } elseif (isset($_REQUEST['oauth_token']) && $_SESSION['token'] == $_REQUEST['oauth_token']) {

            $twitterParam = array(
                "consumer_key" => $this->CONSUMER_KEY,
                "consumer_secret" => $this->CONSUMER_SECRET,
                "oauth_token" => $_SESSION['token'],
                "oauth_token_secret" => $_SESSION['token_secret']
            );
            $this->load->library('twitter/twitteroauth', $twitterParam, 'twitter');
            $access_token = $this->twitter->getAccessToken($_REQUEST['oauth_verifier']);
            if ($this->twitter->http_code == '200') {
                $_SESSION['status'] = "verified";
                $_SESSION['request_vars'] = $access_token;

                unset($_SESSION['token']);
                unset($_SESSION['token_secret']);
                $status = 'verified';
                $additional = 'Logowanie poprawne!';

            } else {
                die("error, try again later!");
            }

        } else {

            if (isset($_GET["denied"])) {
                $status = 'denied';
                $additional = 'Dostęp zabroniony!';
            }

            $twitterParam = array(
                "consumer_key" => $this->CONSUMER_KEY,
                "consumer_secret" => $this->CONSUMER_SECRET,
            );
            $this->load->library('twitter/twitteroauth', $twitterParam, 'twitter');
            $request_token = $this->twitter->getRequestToken($this->OAUTH_CALLBACK);

            $_SESSION['token'] = $request_token['oauth_token'];
            $_SESSION['token_secret'] = $request_token['oauth_token_secret'];
            if ($this->twitter->http_code == '200') {
                $twitter_url = $this->twitter->getAuthorizeURL($request_token['oauth_token']);
                $status = 'redirect';
                $additional = $twitter_url;
            } else {
                die("error connecting to twitter! try again later!");
            }
        }
        $return = array(
            "status" => $status,
            "additional" => $additional
        );
        return $return;
    }

}
