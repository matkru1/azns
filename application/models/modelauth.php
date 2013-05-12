<?php
// session_start();
/**
 *
 */
class ModelAuth extends CI_Model {

    private $CONSUMER_KEY = 'OyplEQjLvJ66a2S1y7gfyQ';
    private $CONSUMER_SECRET = 'zHQ91nyWDctWfb198k0z0KSP4mOwT5yKWqrNej0oaGU';
    private $OAUTH_CALLBACK = 'http://localhost/laska/index.php/login';
    // $, $consumer_secret, $oauth_token

    function __construct() {
        $this->load->helper('url');
        // $this->load->library('firephp');
    }

    public function process() {
        $status = '';
        $additional = '';

        // if (isset($_REQUEST['oauth_token']) && $this->session->userdata('token') !== $_REQUEST['oauth_token']) {
        if (isset($_REQUEST['oauth_token']) && $_SESSION['token'] !== $_REQUEST['oauth_token']) {
            // $this->firephp->log($this->session->all_userdata(), 'old');
            /*
             echo '<pre>';
             echo print_r($this->session->all_userdata());
             echo '</pre>';
             die();*/

            // if token is old, distroy any session and redirect user to index.php
            // $this->session->sess_destroy();
            session_destroy();
            $status = 'old';
            $additional = 'Spróbuj ponownie!';

            // } elseif (isset($_REQUEST['oauth_token']) && $this->session->userdata('token') == $_REQUEST['oauth_token']) {
        } elseif (isset($_REQUEST['oauth_token']) && $_SESSION['token'] == $_REQUEST['oauth_token']) {

            // everything looks good, request access token
            //successful response returns oauth_token, oauth_token_secret, user_id, and screen_name
            $twitterParam = array(
                "consumer_key" => $this->CONSUMER_KEY,
                "consumer_secret" => $this->CONSUMER_SECRET,
                "oauth_token" => $_SESSION['token'],
                "oauth_token_secret" => $_SESSION['token_secret']
            );
            $this->load->library('twitter/twitteroauth', $twitterParam, 'twitter');
            // $connection = new TwitterOAuth($this->CONSUMER_KEY, $this->CONSUMER_SECRET, $_SESSION['token'], $_SESSION['token_secret']);
            $access_token = $this->twitter->getAccessToken($_REQUEST['oauth_verifier']);
            if ($this->twitter->http_code == '200') {
                //redirect user to twitter
                $sess = array(
                    "status" => "verified",
                    "request_vars" => $access_token
                );
                // $this->session->set_userdata($sess);
                $_SESSION['status'] = "verified";
                $_SESSION['request_vars'] = $access_token;

                // unset no longer needed request tokens
                // $this->session->unset_userdata('token');
                // $this->session->unset_userdata('token_secret');
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

            //fresh authentication
            $twitterParam = array(
                "consumer_key" => $this->CONSUMER_KEY,
                "consumer_secret" => $this->CONSUMER_SECRET,
            );
            $this->load->library('twitter/twitteroauth', $twitterParam, 'twitter');
            // $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);
            $request_token = $this->twitter->getRequestToken($this->OAUTH_CALLBACK);

            //received token info from twitter
            $sess = array(
                "token" => $request_token['oauth_token'],
                "token_secret" => $request_token['oauth_token_secret']
            );
            $this->session->set_userdata($sess);
            $_SESSION['token'] = $request_token['oauth_token'];
            $_SESSION['token_secret'] = $request_token['oauth_token_secret'];
            // any value other than 200 is failure, so continue only if http code is 200
            if ($this->twitter->http_code == '200') {
                //redirect user to twitter
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
