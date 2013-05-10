<?php
//start session
session_start();

//just simple session reset on logout click
if(isset($_GET["reset"]) && $_GET["reset"]==1)
{
    session_destroy();
    header('Location: ./index.php');
}

// Include config file and twitter PHP Library by Abraham Williams (abraham@abrah.am)
include_once("config.php");
include_once("twitteroauth.php");
?>
<html>
<head>
<title>Sign-in with Twitter</title>
<link href="twitter_style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div class="wrapper">
<?php

if(isset($_SESSION['status']) && $_SESSION['status']=='verified')
{   //Success, redirected back from process.php with varified status.
    //retrive variables
    $screenname         = $_SESSION['request_vars']['screen_name'];
    $twitterid          = $_SESSION['request_vars']['user_id'];
    $oauth_token        = $_SESSION['request_vars']['oauth_token'];
    $oauth_token_secret = $_SESSION['request_vars']['oauth_token_secret'];

    //Show welcome message
    echo '<div class="welcome_txt">Welcome <strong>'.$screenname.'</strong> (Twitter ID : '.$twitterid.'). <a href="index.php?reset=1">Logout</a>!</div>';
    $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $oauth_token, $oauth_token_secret);

    //see if user wants to tweet using form.
    if(isset($_POST["updateme"]))
    {
        //Post text to twitter
        $my_update = $connection->post('statuses/update', array('status' => $_POST["updateme"]));
        die('<script type="text/javascript">window.top.location="index.php"</script>'); //redirect back to index.php
    }

    //show tweet form
    echo '<div class="tweet_box">';
    echo '<form method="post" action="index.php"><table width="200" border="0" cellpadding="3">';
    echo '<tr>';
    echo '<td><textarea name="updateme" cols="60" rows="4"></textarea></td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td><input type="submit" value="Tweet" /></td>';
    echo '</tr></table></form>';
    echo '</div>';

        //Get latest tweets
       /* $my_tweets = $connection->get('statuses/user_timeline', array('screen_name' => $screenname, 'count' => 5));
        /* echo '<pre>'; print_r($my_tweets); echo '</pre>'; 

        echo '<div class="tweet_list"><strong>Latest Tweets : </strong>';
        echo '<ul>';
        foreach ($my_tweets  as $my_tweet) {
            echo '<li>'.$my_tweet->text.' <br />-<i>'.$my_tweet->created_at.'</i></li>';
        }
        echo '</ul></div>'; */
}else{
    //login button
    echo '<a href="process.php"><img src="images/button.png" width="180" height="29" border="0" /></a>';
}

?>
</div>
</body>
</html>