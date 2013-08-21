<?php
/**
* post_tweet.php
* Example of posting a tweet with OAuth
* Latest copy of this code: 
* http://140dev.com/twitter-api-programming-tutorials/hello-twitter-oauth-php/
* @author Adam Green <140dev@gmail.com>
* @license GNU Public License
*/

$tweet_text = 'Hello Twitter';
print "Posting...\n";
$result = post_tweet($tweet_text);
print "Response code: " . $result . "\n";

function post_tweet($tweet_text) {

  // Use Matt Harris' OAuth library to make the connection
  // This lives at: https://github.com/themattharris/tmhOAuth
  require_once('twitlib/tmhOAuth.php');
      
  // Set the authorization values
  // In keeping with the OAuth tradition of maximum confusion, 
  // the names of some of these values are different from the Twitter Dev interface
  // user_token is called Access Token on the Dev site
  // user_secret is called Access Token Secret on the Dev site
  // The values here have asterisks to hide the true contents 
  // You need to use the actual values from Twitter
  $connection = new tmhOAuth(array(
    'consumer_key' => 'Z6EiKT7pZq6OVqvWtZqxg',
    'consumer_secret' => '3VyLFSlQg70ZOvj2QAY0egPFB0etNAE8kTOQygZ7kew',
    'user_token' => '280185832-LiJ78IFzm1SolSZMaFuAkK5yu1XFMs5hhN48cXS1',
    'user_secret' => 'vOlhWfm9sCXYPoMPZUCZY5RlqEOJECcz6LtlIb4C2XE',
  )); 
  
  // Make the API call
  $connection->request('POST', 
    $connection->url('1/statuses/update'), 
    array('status' => $tweet_text));
  
  return $connection->response['code'];
}
?>