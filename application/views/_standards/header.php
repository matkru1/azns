<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<link rel="stylesheet" href="<?php echo $baseUrl ?>styles/core.css" type="text/css" />
		<title><?php echo $title; ?></title>
		<script type="text/javascript" src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
        
	</head>
	<body>
	    <div class="container">
    	    <div id="login">
    	        <div>
    	            <?php if(isset($userName)) { ?>
    	               Jesteś zalogowany jako <?php echo $userName; ?> <a href="<?php echo $baseUrl; ?>index.php/logout" title="Wyloguj">(wyloguj)</a> 
    	            <?php
                        }
                    ?>
    	        </div>
    	    </div>

