<?php
require_once(dirname(__FILE__).'/model/Http.class.php');
// $cookie = tempnam(dirname(__FILE__).'\tmp', 'cookie');
// $loginCookie = file_get_contents(dirname(__FILE__).'\cookie.tmp');
// echo $loginCookie;
// 登陆页面
// $loginUrl = "http://git.fm/session";
// $user = "luxueyan"; 
// $pass = "luxueyan881123"; 
// $curlPost = "login=".$user."&password=".$pass; 
$data_string = json_encode(array(
	  "title"=>"Found a bug",
	  "body"=>"I'm having a problem with this.",
	  "assignee"=>"octocat",
	  "milestone"=>1,
	  "labels"=>array(
	    "Label1",
	    "Label2"
	  )
	));
$http =  new Http();
// $http->set_header('Authorization','Basic dWRldjpnaXRodWJmb3J1bWVuZ2Rldg==');
// // $http->set_header('Cookie',$loginCookie);
// // $http->set_header('Host','git.fm');
// // $http->set_header('Origin','http://git.fm');
// // $http->set_header('Referer','http://git.fm/session');
// $req =  $http->post($loginUrl,$curlPost,$cookie);
// // echo ($req->text);
$addIssueUrl = "https://api.github.com/repos/luxueyan2008/compass_test/issues";
$http->set_header('Authorization','Basic dWRldjpnaXRodWJmb3J1bWVuZ2Rldg==');
// $http->set_header('Cookie',$loginCookie);
// $http->set_header('Host','git.fm');
// $http->set_header('Origin','http://git.fm');
// $http->set_header('If-None-Match','50f37fff62af164b4b19bdc1d9f1c279');
// $http->set_header('Referer','http://git.fm/ufp-xp/AdsEngDocs');
$reqAccessAddIsuse =  $http->post($addIssueUrl,json_encode($data_string));
echo ($reqAccessAddIsuse->text);
require_once(dirname(__FILE__).'/html/ufp-issue.html.php');
?>