<?php
include("../includes/common.php");
if(empty($_SESSION['ytidc_user']) && empty($_SESSION['ytidc_pass'])){
  	@header("Location: ./login.php");
     exit;
}else{
  	$username = daddslashes($_SESSION['ytidc_user']);
  	$userkey = daddslashes($_SESSION['ytidc_adminkey']);
  	$user = $DB->query("SELECT * FROM `ytidc_user` WHERE `username`='{$username}'");
  	if($user->num_rows != 1){
      	@header("Location: ./login.php");
      	exit;
    }else{
    	$user = $user->fetch_assoc();
      	$userkey1 = md5($_SERVER['HTTP_HOST'].$user['password']);
      	if($userkey != $userkey1){
      		@header("Location: ./login.php");
      		exit;
      	}
    }
}
$title = "服务管理";
include("./head.php");
$result = $DB->query("SELECT * FROM `ytidc_service` WHERE `userid`='{$user['id']}'");
$service_template = file_get_contents("../templates/".$conf['template']."/user_service_list.template");
while($row = $result->fetch_assoc()){
	$service_template_code = array(
		'id' => $row['id'],
		'username' => $row['username'],
		'password' => $row['password'],
		'enddate' => $row['enddate'],
		'product' => $row['product'],
		'status' => $row['status'],
	);
	$service_template_new = $service_template_new . template_code_replace($service_template, $service_template_code);
}
$template = file_get_contents("../templates/".$conf['template']."/user_service.template");
$include_file = find_include_file($template);
foreach($include_file[1] as $k => $v){
		if(file_exists("../templates/".$conf['template']."/".$v)){
			$replace = file_get_contents("../templates/".$conf['template']."/".$v);
			$template = str_replace("[include[{$v}]]", $replace, $template);
		}
		
}
$template_code = array(
	'site' => $site,
	'config' => $conf,
	'template_file_path' => '../templates/'.$conf['template'],
	'user' => $user,
	'service' => $service_template_new,
);
$template = template_code_replace($template, $template_code);
echo $template;

?>