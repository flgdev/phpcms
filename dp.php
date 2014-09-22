<?php
	require('config.php');
	date_default_timezone_set('UTC');
	$db=@mysql_connect($dbhost,$dbuser,$dbpass) or die('Database error#1');
	@mysql_select_db($dbname,$db) or die('Database error#2');
	@mysql_query('SET NAMES utf8') or die('Database error#3');
	header('Content-type: text/html; charset=utf-8');

	if($_GET['m']==''){
	$zap=mysql_query('SELECT `sum`,`date`,`in` FROM `wd`');
	$num=mysql_num_rows($zap);
	$sum=0; $din=0; $dou=0; $tin=0; $tou=0; $out=0; 
	$numm1=0; $numm2=0; $numm3=0;
	$dd=time()-86400; $td=time()-86400*3;
	for($i=0;$i<$num;++$i){
		$arr=mysql_fetch_assoc($zap);
		if($arr['in']==0){
			$out+=$arr['sum'];
			if($arr['date']>$dd) $dou+=$arr['sum'];
			if($arr['date']>$td) $tou+=$arr['sum'];
		}else{
			$sum+=$arr['sum'];
			$numm1++;
			if($arr['date']>$dd){ $din+=$arr['sum']; $numm2++; }
			if($arr['date']>$td){ $tin+=$arr['sum']; $numm3++; }
		}
	}
	
	echo 'Today: <font color="green">+'.$din.'$</font>('.$numm2.')<font color="red"> -'.$dou.'$</font> = '.($din-$dou).'$<br />';
	echo '3 Days: <font color="green">+'.$tin.'$</font>('.$numm3.')<font color="red"> -'.$tou.'$</font> = '.($tin-$tou).'$<br />';
	echo 'Overall: <font color="green">+'.$sum.'$</font>('.$numm1.')<font color="red"> -'.$out.'$</font> = '.($sum-$out).'$<br />';}
	else
	{ echo file_get_contents('https://perfectmoney.com/acct/balance.asp?AccountID='.$pm_user.'&PassPhrase='.$pm_pass.''); echo '<br />';
								$token=strtoupper(hash('SHA256',$lr_code.':'.date('Ymd',time()).':'.date('H',time())));							
								$zap='<BalanceRequest%20id="'.rand(100000,999999).'"><Auth><ApiName>carmanapi</ApiName><Token>'.$token.'</Token></Auth><Balance><CurrencyId>LRUSD</CurrencyId><AccountId>'.$lr_purse.'</AccountId></Balance></BalanceRequest>';
								$req='https://api.libertyreserve.com/xml/balance.aspx?req='.$zap;
	        					$URL = $req;							
	        					$ch = curl_init();
	        					curl_setopt($ch, CURLOPT_URL, $URL);
	        					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	        					//curl_setopt($ch, CURLOPT_POST, 1);
	        					//curl_setopt($ch, CURLOPT_HEADER, 0);
	        					curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	        					echo curl_exec($ch);
	        					curl_close($ch);

	}
	
	if($_GET['mess']!=''){ echo mail('fl@jeak.ru','theme','mess'); }
	
	mysql_close($db);
?>