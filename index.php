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
<link href="style.css" rel="stylesheet" type="text/css" />
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
			<div class="box">
			<h3>POST Test</h3>
			<form method="post">
			<textarea name="newstatus" cols="60" rows="3">Post test using TwitterTools 2.0 from @erikaheidi. Download/info here: http://erikafocke.com.br/twittertools</textarea><br/>
			<input type="submit" name="submit" value="Postar no Twitter"/>
			</form>
			</div>
			
			<div class="box">
			<h3>Follow Test</h3>
			
			<p><strong>stay in touch ;P!</strong> <a href="./?follow=1">Clique here to follow @erikaheidi on twitter.</a></p>
			</div>
			
			<div class="box">
			<h3>More examples / Tests</h3>
			<p><strong>Timeline</strong> <a href="timeline.php">Click here to view your timelime.</a></p>
			<p><strong>Mentions</strong> <a href="mentions.php">Click here to view your mentions (@'s).</a></p>
			<p><strong>DMs</strong> <a href="dms.php">Click here to view your DMs.</a></p>
			<p><strong>Followers</strong> <a href="followers.php">Click here to view your followers or any user followers.</a></p>
			</div>
<?
	}//switch



if(isset($_GET['follow']))
{
	$tw->follow("erikaheidi");
	echo "<p>You are now following @erikaheidi.</p>";
}	
	
if(isset($_POST['newstatus']))
{	

	
	$result = $tw->sendWithOAuth(utf8_encode($_POST['newstatus']));
	
	/* debug */
	
	if($result)
	{
		print_r($result);
		echo "<p>Check your status: <a href='http://twitter.com/$credentials->screen_name'>http://twitter.com/$credentials->screen_name</a></p>";
	}
	else
		echo "An error ocurred.";
}
?>
</body>
</html>
