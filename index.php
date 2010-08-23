<?php
//essencial!
session_start();

if(isset($_GET['logout']))
	session_unset();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>TwitterTools - DEMO</title>
<link href="examples/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<h2>Twitter Tools Demo</h2>
<?php
require_once("lib/TwitterTools.php");
require_once("lib/TwitterOAuth.php");
require_once("lib/OAuth.php");

	/* consumer key & consumer secret - register an app to get yours at:
	 * http://dev.twitter.com/apps/new
	 */
	$consumer_key = "vWJfl9R2BrOcYiS1sLK2A";
	$consumer_secret = "AfWYuCgPtpt4HK82djgZbukmjbSIPQ49Yqvvzkpw";
	
	$tw = new TwitterTools($consumer_key,$consumer_secret);
	$state = $tw->checkState();
	/*
	 * possible states:
	 * start - not authenticated, first access
	 * returned - user just authorized your app and returned
	 * logged - user are logged in
	 */ 
	switch($state)
	{
		case "start":
			
			$request_link = $tw->getAuthLink();
			echo '<h3>Sign in with your twitter account</h3>';
			echo '<p><a href="'.$request_link.'" title="sign in with your twitter account"><img src="img/sign-in-with-twitter-d.png" /></a></p>';
			
			break;

		case "returned":
			$tw->getAccessToken();

		case "logged":

			$credentials = $tw->getCredentials();
			
			?>
			<p>You are logged in as: <strong><?=$credentials->screen_name?></strong> [ <a href="./?logout=1">LOGOUT</a> ]</p>

			
<?
	}//switch
?>
			<div class="box">
			<h3>All examples / Tests</h3>
			<p><strong>Update</strong> <a href="examples/update.php">You can update your status in this example.</a></p>
			<p><strong>Follow</strong> <a href="examples/follow.php">Click here to check a follow example.</a></p>
			<p><strong>Timeline</strong> <a href="examples/timeline.php">Click here to view your timelime.</a></p>
			<p><strong>Mentions</strong> <a href="examples/mentions.php">Click here to view your mentions (@'s).</a></p>
			<p><strong>DMs</strong> <a href="examples/dms.php">Click here to view your DMs.</a></p>
			<p><strong>Followers</strong> <a href="examples/followers.php">Click here to view your followers or any user followers.</a></p>
			</div>
</body>
</html>
