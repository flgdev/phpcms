<?php
	require('config.php');
	$db=@mysql_connect($dbhost,$dbuser,$dbpass) or die('Database error#1');
	@mysql_select_db($dbname,$db) or die('Database error#2');
	@mysql_query('SET NAMES utf8') or die('Database error#3');
	header('Content-type: text/html; charset=utf-8');

	function depos($sum,$uid){
		$zam=mysql_query('select `ref`,`proc` from `users` where `id`="'.$uid.'"');
		$zzz=mysql_fetch_assoc($zam);
		$proc=mysql_result(mysql_query('select `proc` from `users` where `id`="'.$zzz['ref'].'"'),0,'proc');
		$feed=(0.07)*$sum;
		mysql_query('update `users` set `amount`=`amount`+"'.$feed.'" where `id`="'.$zzz['ref'].'"');
		mysql_query('update `users` set `toref`=`toref`+"'.$feed.'" where `id`="'.$uid.'"');
		mysql_query('INSERT INTO `depos` (`user_id` ,`proc` ,`date` ,`paid` ,`bet` )VALUES ("'.$uid.'", "'.$zzz['proc'].'", "'.time().'", "0", "'.$sum.'")');
	}	
	
if(isset($_REQUEST['lr_paidto'])){
	$e=0;
$str = 
  $_REQUEST["lr_paidto"].":".
  $_REQUEST["lr_paidby"].":".
  $_REQUEST["lr_store"].":".
  $_REQUEST["lr_amnt"].":".
  $_REQUEST["lr_transfer"].":".
  $_REQUEST["lr_currency"].":".
  $lr_code;
  $hash = strtoupper(hash('SHA256', $str));
  
  if($hash != $_REQUEST["lr_encrypted"]){ $e++; }
	$str1=$str;
 $str = 
	$_REQUEST['lr_paidto'].":".
	$_REQUEST['lr_paidby'].":".
	$_REQUEST['lr_store'].":".
	$_REQUEST['lr_amnt'].":".
	$_REQUEST['lr_transfer'].":".
	$_REQUEST['lr_merchant_ref'].":".
	'uid='.$_REQUEST['uid'].":".
	$_REQUEST['lr_currency'].":".
	$lr_code;

  $hash2 = strtoupper(hash('SHA256', $str));	
  
	if($hash2!=$_REQUEST['lr_encrypted2']){ $e++; }
	/*
	$req='';
	foreach($_REQUEST as $key => $value){
		$req=$key.'|'.$value."\n";
	}
	file_put_contents('1.txt',$str1.'|'.$hash.'|'.$_REQUEST["lr_encrypted"].'||'.$str.'|'.$hash2.'|'.$_REQUEST['lr_encrypted2']."\n".$req);*/

	if($e==0){
		depos($_REQUEST['lr_amnt'],$_REQUEST['uid']);
		mysql_query('INSERT INTO `wd` (`user` ,`sum` ,`pm`,`in`,`purse`,`date` )VALUES ("'.$_REQUEST['uid'].'", "'.$_REQUEST['lr_amnt'].'", "0","1","'.$_REQUEST['lr_paidby'].'","'.time().'")');
	}
}

if(isset($_REQUEST['manu'])){
	if(empty($_REQUEST['sub'])){
	echo'
	<form action="?" method="post">
	User id <input type="text" name="uid"><br />
	Amount <input type="text" name="sum"><br />
	Purse <input type="text" name="kosh"><br />
	<input type="hidden" name="manu" value="1">
	<input type="submit" name="sub" value="OK">
	';}else{
		depos($_REQUEST['sum'],$_REQUEST['uid']);
		mysql_query('INSERT INTO `wd` (`user` ,`sum` ,`pm`,`in`,`purse`,`date` )VALUES ("'.$_REQUEST['uid'].'", "'.$_REQUEST['sum'].'", "0","1","'.$_REQUEST['kosh'].'","'.time().'")');
		echo'ok';
	}
}

if(isset($_REQUEST['PAYMENT_ID'])){
	$str=
      $_REQUEST['PAYMENT_ID'].':'.
	  $_REQUEST['PAYEE_ACCOUNT'].':'.
      $_REQUEST['PAYMENT_AMOUNT'].':'.
	  $_REQUEST['PAYMENT_UNITS'].':'.
      $_REQUEST['PAYMENT_BATCH_NUM'].':'.
      $_REQUEST['PAYER_ACCOUNT'].':'.(strtoupper(md5($code))).':'.$_REQUEST['TIMESTAMPGMT'];
	 
	$hash=strtoupper(md5($str));
	$uid=substr($_REQUEST['PAYMENT_ID'],3,99);
	$sum=$_REQUEST['PAYMENT_AMOUNT'];
	
	if($hash==$_REQUEST['V2_HASH']){
		depos($sum,$uid);
		mysql_query('INSERT INTO `wd` (`user` ,`sum` ,`pm`,`in`,`purse`,`date` )VALUES ("'.$uid.'", "'.$sum.'", "1","1","'.$_REQUEST['PAYER_ACCOUNT'].'","'.time().'")');
	}
}
	mysql_close($db);
?>