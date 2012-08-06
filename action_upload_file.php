<?php

require_once(dirname(__FILE__).'/lib/php.php');
require_once(dirname(__FILE__).'/model/Http.class.php');
$responseText = "";
  // var_dump($_POST);
function github_get($github_url){
	$data_string = json_encode(array(
		// 'login'=>'luxueyan2008',
		// 'token' => 'Basic dWRldjpnaXRodWJmb3J1bWVuZ2Rldg==',
	  "title"=>"Found a bug",
	  "body"=>"I'm having a problem with this.",
	  "assignee"=>"octocat",
	  "milestone"=>1,
	  "labels"=>array(
	    "Label1",
	    "Label2"
	  )
	));
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$github_url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST"); 
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization:Basic dWRldjpnaXRodWJmb3J1bWVuZ2Rldg=='));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);  
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); 
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,1);
    curl_setopt($ch, CURLOPT_USERPWD, "luxueyan:luxueyan881123");
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);   
    $data = curl_exec($ch);
    curl_close($ch);
    return ($data);
    echo(json_decode($data,true));
}
echo(github_get('http://git.fm/luxueyan/AdsEngDocs/issues'));
if (!empty($_FILES)) {
	require_once(dirname(__FILE__). '/lib/Github/Autoloader.php');
	Github_Autoloader::register();
	$files=$_FILES['images'];
	// var_dump($files);
	foreach($files['name'] as $key=>$file){
		$tempFile = $files['tmp_name'][$key];
		$imgsize=$files['size'][$key];
		$filename=$files['name'][$key];
		$realFile='images/'.getToken().$files['name'][$key];
		move_uploaded_file($tempFile,$realFile);
		// echo $filename;
		$responseText .="<img src='".$realFile."'/>" ;
	}
	// echo exec('commit.bat');
	$user = "luxueyan"; 
	$pass = "luxueyan881123"; 
	$method = Github_Client::AUTH_HTTP_TOKEN;
 	$github = new Github_Client();
 	$github->authenticate($user, $pass, $method);
 	// $res = $github->getIssueApi()->getList('luxueyan2008', 'compass_test', 'open');
 	// var_dump($res);
 	$github->getIssueApi()->open('luxueyan', 'AdsEngDocs', 'The issue title', 'The issue body');
}
echo $responseText;
?>