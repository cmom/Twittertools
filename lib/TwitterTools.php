<?php
/* ***************************************************************
 * TwitterTools 
 * v. 2.0 - 22/10/2010
 * by @erikaheidi
 * http://www.erikafocke.com.br/twittertools
 *****************************************************************/

class TwitterTools{
	
	/* Bit.ly info ! 
	 * register here: http://www.bit.ly/account/register?rd=/
	 * api key: http://bit.ly/account/your_api_key/
	 */
	static $bl_login = "generictest";
	static $bl_apikey = "R_08f8a800201f625589a37f057197fa69";
	
	var $consumer_key;
	var	$consumer_secret;

	var $access_token;
	var $access_secret;
	
	/* __construct
	 * $consumer_key = twitter app consumer key
	 * $consumer_secret = twitter app consumer secret
	 */
	function __construct($consumer_key,$consumer_secret)
	{
		$this->consumer_key = $consumer_key;
		$this->consumer_secret = $consumer_secret;
		
		/* if registered, get user tokens from session*/
		$this->atoken =  $_SESSION['oauth_access_token'];
		$this->atoken_secret =  $_SESSION['oauth_access_token_secret'];
	}
	
	
	function checkState()
	{
		
		if(isset($_SESSION['oauth_state']) && !empty($this->atoken))
			$state = $_SESSION['oauth_state'] = "logged";
		elseif($_REQUEST['oauth_token'] != NULL && $_SESSION['oauth_state'] === 'start') 
			$state = $_SESSION['oauth_state'] = "returned";
		else
			$state = $_SESSION['oauth_state'] = "start";
			
		return $state;
	}
	
	function getAuthLink()
	{
		$to = new TwitterOAuth($this->consumer_key, $this->consumer_secret);
		$tok = $to->getRequestToken();

		$_SESSION['oauth_request_token'] = $token = $tok['oauth_token'];
		$_SESSION['oauth_request_token_secret'] = $tok['oauth_token_secret'];

		return $to->getAuthorizeURL($token);
	}
	
	function getAccessToken()
	{
		$to = new TwitterOAuth($this->consumer_key, $this->consumer_secret, $_SESSION['oauth_request_token'], $_SESSION['oauth_request_token_secret']);			 
		$tok = $to->getAccessToken();

		$_SESSION['oauth_access_token'] = $this->atoken = $tok['oauth_token'];
		$_SESSION['oauth_access_token_secret'] = $this->atoken_secret = $tok['oauth_token_secret'];
	}
	
	/*
	 * shortcut for checking user state
	 */ 
	function logged()
	{
		return !empty($this->atoken);
	}
	
	/*
	 * get user credentials
	 * return
	 */ 
	function getCredentials()
	{
		$user = $this->makeRequest('http://api.twitter.com/account/verify_credentials.xml');
		if($user)
			return simplexml_load_string($user);
	}

	function sendWithOAuth($msg)
	{		
		$message= strip_tags($msg);
		
		$pos = strpos($message,"http://");
		if ($pos !== false) 
		{
			$aux = substr($message,$pos);
			$split = explode(" ",$aux);
			$theUrl = $split[0];
			$small = $this->getSmallLink($theUrl);
			$message= str_replace($theUrl,$small,$message);			
		}
		$message = substr($message,0,140);
			
		
		return $this->makeRequest('http://api.twitter.com/statuses/update.xml', array('status' => $message), 'POST');
	}
	
	
	//bit.ly
	function getSmallLink($longurl) 
	{	
		$login = self::$bl_login;
		$apiKey = self::$bl_apikey;
		$url = "http://api.bit.ly/shorten?version=2.0.1&longUrl=$longurl&login=$login&apiKey=$apiKey&format=json&history=1";
		$result = file_get_contents($url);
		$obj = json_decode($result, true);
		return $obj ["results"] ["$longurl"] ["shortUrl"];
	}

	function follow($to)
	{
		return $this->makeRequest('http://api.twitter.com/1/friendships/create.xml', array("screen_name"=>$to), 'POST');
	}
	
	function getTimeline($limit=10)
	{	
		$ret= $this->makeRequest('http://api.twitter.com/1/statuses/home_timeline.xml',array("count"=>$limit));		
		if($ret)
		{
			$all = simplexml_load_string($ret);
			return $all->status;
		}
	}
	
	function getMentions($limit=10)
	{	
		$ret = $this->makeRequest('http://api.twitter.com/1/statuses/mentions.xml',array("include_rts"=>1,"count"=>$limit));
		if($ret)
		{			
			$all = simplexml_load_string($ret);
			return $all->status;
		}
	}
	
	function getDms($limit=10)
	{

		$ret = $this->makeRequest('http://api.twitter.com/1/direct_messages.xml',array("cursor"=>$limit));
		if($ret)
		{
			$all = simplexml_load_string($ret);
			return $all->direct_message;
		}
	}
		
	function getFollowers($screen_name,$limit=10)
	{
	
		$result = $this->makeRequest('http://api.twitter.com/1/followers/ids.xml',array("screen_name"=>$screen_name,"cursor"=>"-1"));
		
		$ids = simplexml_load_string($result);
	
		$c=0;
		foreach($ids->ids->id as $id)
		{
			if(!$c)
				$lista = $id;
			else
				$lista .= ",".$id;
			
			$c++;
			if($c == $limit)
				break;
		}
		
		
		return $this->getUsersInfo($lista); 
		
	}
	
	function getUsersInfo($lista_users)
	{
		$ret = $this->makeRequest('http://api.twitter.com/1/users/lookup.xml',array("user_id"=>$lista_users));
		if($ret)
		{
			$all = simplexml_load_string($ret);
			return $all->user;
		}		
	}
	
	function makeRequest($api_url,$args=null,$method='GET')
	{
		$twitter = new TwitterOAuth($this->consumer_key, $this->consumer_secret, $this->atoken, $this->atoken_secret);
		
		return $twitter->OAuthRequest($api_url,$args,$method);
	}
}


?>
