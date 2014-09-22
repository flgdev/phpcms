<?php
	require('config.php');
	date_default_timezone_set('UTC');
	@mysql_connect($dbhost,$dbuser,$dbpass) or die('Database error#1');
	@mysql_select_db($dbname) or die('Database error#2');
	@mysql_query('SET NAMES utf8') or die('Database error#3');
	header('Content-type: text/html; charset=utf-8');
	session_start();
	if(isset($_GET['l'])){setcookie('ref',htmlspecialchars($_GET['l']));}
	if(isset($_SESSION['nick'])){$login=$_SESSION['nick']; $a=true;}else{$login=''; $a=false;}
	if($login!='') $myid=mysql_result(mysql_query('select `id` from `users` where `login`="'.$login.'"'),0,'id');
	if(isset($_GET['lang'])){$lang=substr($_GET['lang'],0,2);}
	if(file_exists('inc/'.$lang.'.php')){ setcookie("lang", $lang); }else
	{
		if(isset($_COOKIE['lang'])){$lang=substr($_COOKIE['lang'],0,2);}
		if(!file_exists('inc/'.$lang.'.php')){$lang='en';}
	}
	include('inc/'.$lang.'.php');
	$page='';
	function inbrs($s){ global $a; if(1){return str_replace("\n",'<br />',$s);}else{return str_replace("\n",'<br />','<center>'.$s.'</center>');} }
	function clr($s){  return iconv_substr(mysql_real_escape_string(htmlspecialchars(trim($s))),0,100,'utf-8'); }
	
	function amoney($sum,$feed,$day,$ld,$co,$proc)
	{
		if(($proc==15)&&($ld>20)){ $ld=20; }
		if(($proc==17)&&($ld>30)){ $ld=30; }
		if(($proc==19)&&($ld>40)){ $ld=40; }
		if(($proc==22)&&($ld>60)){ $ld=60; }
		if($day<=$ld)
		{
			$b=$sum*$proc/1000;
			$sum+=$b*($co/100);
			$feed+=$b*(1-$co/100);
		}
		if($day>=$ld){ return $sum.'|'.$feed; }else{ return amoney($sum,$feed,$day+1,$ld,$co,$proc); }
	}
	
	function proc($day,$dep)
	{
		$a=0;
		if($dep==0)
		{
			$a=1.8;
			if ($day>29) $a=2.0;
			if ($day>89) $a=2.2;
			if ($day>179) $a=2.4;
			if ($day>269) $a=2.6;
			if ($day>364) $a=2.8;
		}
		
		if($dep==1)
		{
			$a=50;
		}
		
		if($dep==2)
		{
			$a='A';
			if ($day>29) $a='B';
			if ($day>89) $a='C';
			if ($day>179) $a='D';
			if ($day>269) $a='E';
			if ($day>364) $a='F';
		}
		
		return $a;
	}
	
	function sround($tt,$val)
	{
		for($i=0;$i<$tt;$i++){ $val*=10; }
		$val=floor($val);
		for($i=0;$i<$tt;$i++){ $val/=10; }
		if($val<0)$val=0;
		return $val;
	}
	
	function brip()
	{
		return $_SERVER['REMOTE_ADDR'].'|'.$_SERVER['HTTP_USER_AGENT'];
	}

				if($_REQUEST['m']=='in'){
					$auth=0;
					if(isset($_REQUEST['login'])){
					if(isset($_REQUEST['login'])){$login=clr($_REQUEST['login']);}else{$login='';}
					if(isset($_REQUEST['pass'])){$pass=clr($_REQUEST['pass']);}else{$pass='';}
					if(($login!='')&&($pass!=''))
					if(mysql_num_rows(mysql_query('select `id` from `users` where (`login`="'.$login.'") and (`pass`="'.$pass.'")'))>0){
						$auth=1;
						$myid=mysql_result(mysql_query('select `id` from `users` where `login`="'.$login.'"'),0,'id');
						$rein=mysql_result(mysql_query('select `reinv` from `users` where `login`="'.$login.'"'),0,'reinv');
						$_SESSION['nick']=$login;
						$zap=mysql_query('select * from `depos` where `user_id`="'.$myid.'"');
						if (mysql_num_rows($zap)>0){
							while(1){
								$arr=mysql_fetch_assoc($zap);
								if(empty($arr['bet']))break;
								$aa=floor((time()-$arr['date'])/86400);
								//if(proc($aa,1) != proc($arr['paid'],1))
								//mail($_POST['mail'],'Notification from '.$domain,MAIL_CAT,'from:faq@'.$domain);
								list($r1,$r2)=explode('|',amoney($arr['bet'],0,$arr['paid']+1,$aa,$rein,$arr['proc']));
								mysql_query('update `users` set `lastdate`="'.time().'",`amount`=`amount`+"'.$r2.'",
								`brip`="'.brip().'" where `login`="'.$login.'"');
									if((($arr['proc']==15)&&($aa>=20))||
									(($arr['proc']==17)&&($aa>=30))||
									(($arr['proc']==19)&&($aa>=40))||
									(($arr['proc']==22)&&($aa>=60))){ $deld=123; }
								if($deld==123){
									mysql_query('delete from `depos` where `dep_id`="'.$arr['dep_id'].'" ');
									mysql_query('update `users` set `amount`=`amount`+"'.$r1.'" where `login`="'.$login.'"');
								}else{
									mysql_query('update `depos` set `bet` = "'.$r1.'", `paid`="'.$aa.'" where `dep_id`="'.$arr['dep_id'].'" ');
								}
							}
						}
						header('location:index.php?m=account'); die('');
					}
					}}

			if(isset($_REQUEST['m'])){$m=htmlspecialchars($_REQUEST['m']);}
			
			
			switch($m){					
				case 'out':
					session_unset();    
					session_destroy();			
				break;
			
				case 'in':
					
				break;
				
				case 'forget':
					$auth=0;
					if(isset($_REQUEST['login'])){$login=clr($_REQUEST['login']);}else{$login='';}
					if(isset($_REQUEST['mail'])){$mail=clr($_REQUEST['mail']);}else{$mail='';}
					if(($_POST['code']!=$_SESSION['code'])||($_SESSION['code']=='')){ $e++; if(isset($_REQUEST['login']))$err=I_CAP; $auth=-1; }else{
					$_SESSION['code']='';
					if(($login!='')||($mail!=''))
					if(mysql_num_rows(mysql_query('select `login`,`pass`,`mail` from `users` where (`login`="'.$login.'") or (`mail`="'.$mail.'")'))>0){
						$auth=1;
						$zap=mysql_query('select `login`,`pass`,`mail` from `users` where (`login`="'.$login.'") or (`mail`="'.$mail.'")');
						$arrr=mysql_fetch_assoc($zap);
						mail($mail,'Notification from '.$domain,MAIL_FOG.$arrr['pass'],'from:faq@'.$domain);
						header('location:?m=home'); die('');
					}else{$err=NOT_FND;}
					}
				break;
			
				case 'reg':
					if(!empty($_POST['sub'])){ $e=0;
						if(($_POST['code']!=$_SESSION['code'])||($_SESSION['code']=='')){ $e++; $er['5']=I_CAP; }else{ $_SESSION['code']=''; }
						if($e==0) if(empty($_POST['log'])){$er[0]=I_LOGIN; $e++;}else{$_POST['log']=clr($_POST['log']); 
								$_POST['pass']=clr($_POST['pass']); $_POST['repass']=clr($_POST['repass']);}
						if($e==0) if(preg_match('/^[a-zA-Z0-9-_]{3,20}$/',$_POST['log'])==0){ $e++; $er[0]=I_LOGIN; }
						if($e==0) if(mysql_num_rows(mysql_query('select `id` from `users` where `login`="'.$_POST['log'].'" '))>0){ $e++; $er[0]=LOGIN_USED; }
						if($e==0) if(((Iconv_strlen($_POST['pass'],'utf-8'))>20) or ((Iconv_strlen($_POST['pass'],'utf-8'))<4)){ $e++; $er[1]=PWD_LEN; }
						if($e==0) if($_POST['pass']!=$_POST['repass']){$er[1]=PWD_DIFF; $e++;}
						if($e==0) if(empty($_POST['mail'])){$er[2]=E_MAIL; $e++;}else{$_POST['mail']=clr($_POST['mail']);
								$_POST['lr']=clr($_POST['lr']); $_POST['pm']=clr($_POST['pm']);}
						if($e==0) if(preg_match('/^[0-9a-zA-Z\._-]{1,50}[@]{1}[0-9a-z-]{1,25}[\.]{1}[a-z]{1,4}$/',$_POST['mail'])==0){ $e++; $er[2]=I_MAIL; }
						if($e==0) if(mysql_num_rows(mysql_query('select `id` from `users` where `mail`="'.$_POST['mail'].'" '))>0){ $e++; $er[2]=MAIL_USED; }
						if($e==0) if((!empty($_POST['lr']))&&(preg_match('/^[UuXx]{1}[0-9]{3,10}$/',$_POST['lr'])==0)){ $e++; $er[3]=I_LR; }
						if($e==0) if((!empty($_POST['pm']))&&(preg_match('/^[UuXx]{1}[0-9]{3,10}$/',$_POST['pm'])==0)){ $e++; $er[4]=I_PM; }
						if ($e>0) for($i=0;$i<6;$i++){if(!empty($er[$i])) $er[$i]='&nbsp;<font color="red">'.$er[$i].'</font>'; }
						
						if($e==0){
							$refid=@mysql_result(mysql_query('select `id` from `users` where `login`="'.$_COOKIE['ref'].'"'),0,'id');
							mysql_query('
								INSERT INTO `users` (`login` ,`pass` ,`ref` ,`regdate` ,`lastdate` ,`lr` ,`pm` ,
								`mail` ,`proc` ,`amount` ,`reinv` ,`toref` ,`brip` )
								VALUES ("'.$_POST['log'].'", "'.$_POST['pass'].'", "'.$refid.'", "'.time().'", "0", "'.$_POST['lr'].'", "'.$_POST['pm'].'",
								"'.$_POST['mail'].'", "15", "0", "0", "0", "'.brip().'")
							');
							mail($_POST['mail'],'Notification from '.$domain,MAIL_REG,'from:faq@'.$domain);
							header('location:?m=in&login='.$_POST['log'].'&pass='.$_POST['pass']); die('');
						}
					}				
				break;

				case 'account':
					if($login==''){break;}
					if(isset($_POST['csub'])){
						$_POST['reinv']=intval($_POST['reinv']);
						if(($_POST['reinv']<0)||($_POST['reinv']>100)){$_POST['reinv']=100;}
						mysql_query('update `users` set `reinv`="'.$_POST['reinv'].'" where  `login`="'.$login.'"');
					}
					$zap=mysql_query('select `sum` from `wd` where (`in`="1")and(`user`="'.$myid.'")');
					$num=mysql_num_rows($zap); $dsum=0;
					for($i=0;$i<$num;$i++){ $ar=mysql_fetch_assoc($zap); $dsum+=$ar['sum']; }
					$zap=mysql_query('select `sum` from `wd` where (`in`="0")and(`user`="'.$myid.'")');
					$num=mysql_num_rows($zap); $wsum=0;
					for($i=0;$i<$num;$i++){ $ar=mysql_fetch_assoc($zap); $wsum+=$ar['sum']; }				
					$zap=mysql_query('select * from `users` where `login`="'.$login.'"');
					$arr=mysql_fetch_assoc($zap);
					$ref=mysql_num_rows(mysql_query('select `id` from `users` where `ref`="'.$arr['id'].'"'));
					$ds=$arr['paid'];
					for($i=0;$i<101;$i+=20)
						if($arr['reinv']==$i){$rsel[$i]='selected';}else{$rsel[$i]='';}
				break;
			
				case 'deposit':
					if($login==''){break;}
					$z=mysql_query('select * from `users` where `login`="'.$login.'"');
					$arr=mysql_fetch_assoc($z);
					$arr['amount']=sround(2,$arr['amount']);
					if(isset($_POST['sub'])){
						$_POST['amount']=sround(2,$_POST['amount']);
						$pro=15;
						switch(intval($_POST['pl'])){
							case 1: 
								if($_POST['amount']<10){$pro=-1;}else{$pro=17;}
							break;
							
							case 2: 
								if($_POST['amount']<100){$pro=-1;}else{$pro=19;}
							break;
							
							case 3: 
								if($_POST['amount']<500){$pro=-1;}else{$pro=22;}
							break;
						}
						if(($_POST['amount']<1)or($pro==-1)){ $err=' <font color="red">'.I_AMOUNT.'</font>';}
						else{ mysql_query('update `users` set `proc`="'.$pro.'" where `id`="'.$myid.'"'); }
						$su=$arr['amount']-$_POST['amount'];
						if($err==''){
							if(($_POST['sys']==0)&&($err=='')){
						if($su>0){
							$sum=$_POST['amount'];
							$zam=mysql_query('select `ref`,`proc` from `users` where `id`="'.$myid.'"');
							$zzz=mysql_fetch_assoc($zam);
							$proc=mysql_result(mysql_query('select `proc` from `users` where `id`="'.$zzz['ref'].'"'),0,'proc');
							$feed=(0.07)*$sum;
							mysql_query('update `users` set `amount`=`amount`+"'.$feed.'" where `id`="'.$zzz['ref'].'"');
							mysql_query('update `users` set `toref`=`toref`+"'.$feed.'", `amount`=`amount`-"'.$sum.'" where `id`="'.$myid.'"');
							mysql_query('INSERT INTO `depos` (`user_id` ,`proc` ,`date` ,`paid` ,`bet` )VALUES ("'.$myid.'", "'.$zzz['proc'].'", "'.time().'", "0", "'.$sum.'")');
							header('location:?m=account'); die('');
						}else{ $err=' <font color="red">'.I_AMOUNT.'</font>'; }
							}else{
							$sum=$_POST['amount']; 
							}
							if(($_POST['sys']==1)&&($err=='')){
					$page='
					<form action="https://sci.libertyreserve.com/en" method="get" id="reg_form">	
						<input type="hidden" name="lr_acc" value="'.$lr_purse.'">
						<input type="hidden" name="lr_store" value="'.$sitename.'">
						<input type="hidden" name="lr_acc_from" value="'.$arr['lr'].'">
						<input type="hidden" name="lr_amnt" value="'.$sum.'">
						<input type="hidden" name="lr_currency" value="LRUSD">
						<input type="hidden" name="lr_comments" value="Deposit to '.$domain.'">
						<input type="hidden" name="lr_success_url" value="http://'.$domain.'/index.php">
						<input type="hidden" name="lr_success_url_method" value="POST">
						<input type="hidden" name="lr_fail_url" value="http://'.$domain.'/index.php">
						<input type="hidden" name="lr_fail_url_method" value="POST">
						<input type="hidden" name="lr_status_url" value="https://'.$domain.'/check.php">
						<input type="hidden" name="lr_status_url_method" value="POST">
						<input type="hidden" name="uid" value="'.$myid.'">
						<p><div style="width:200px;margin:0 auto 0 auto;">
						<input type="submit" class="button" style="width:192px;" value="Pay '.$sum.'$ with '.LR.'" /></div></p>
					</form>
					';	
							}
							if(($_POST['sys']==2)&&($err=='')){
					$page='
					<form action="https://perfectmoney.com/api/step1.asp" method="POST" id="reg_form">
						<input type="hidden" name="PAYEE_ACCOUNT" value="'.$pm_purse.'">
						<input type="hidden" name="PAYEE_NAME" value="'.$sitename.'">
						<input type="hidden" name="PAYMENT_ID" value="'.rand(100,999).$myid.'">
						<input type="hidden" name="PAYMENT_AMOUNT" value="'.$sum.'">
						<input type="hidden" name="PAYMENT_UNITS" value="USD">
						<input type="hidden" name="STATUS_URL" value="https://'.$domain.'/check.php">
						<input type="hidden" name="STATUS_URL_METHOD" value="POST">
						<input type="hidden" name="PAYMENT_URL" value="http://'.$domain.'">
						<input type="hidden" name="PAYMENT_URL_METHOD" value="POST">
						<input type="hidden" name="NOPAYMENT_URL" value="http://'.$domain.'">
						<input type="hidden" name="NOPAYMENT_URL_METHOD" value="POST">
						<input type="hidden" name="SUGGESTED_MEMO" value="Deposit to '.$domain.'">
						<p><div style="width:200px;margin:0 auto 0 auto;">
						<input type="submit" class="button" style="width:192px;" name="PAYMENT_METHOD" value="'.PAY.' '.$sum.'$ '.WITH.' '.PM.'" /></div></p>
					</form>';					
							}
						}
					}
				break;
			
				case 'withdraw':
					if($login==''){ break;}
					$arr=mysql_fetch_assoc(mysql_query('select `amount`,`lr`,`pm` from `users` where `id`="'.$myid.'"'));
					$arr['money']=sround(2,$arr['amount']);
					if(isset($_POST['sub']))
					{
						$_POST['amount']=sround(2,$_POST['amount']);
						if(($_POST['amount']<0.01)||($_POST['amount']>$arr['money'])){$err='&nbsp;<font color="red">'.I_AMOUNT.'</font>';}
						
						if((($_POST['sys']==1)||($_POST['sys']==2))&&(empty($err)))
						{
							if($_POST['sys']==1){$purse=$arr['lr'];}else{$purse=$arr['pm'];}
							mysql_query('UPDATE `users` SET `amount` = `amount`-'.$_POST['amount'].' WHERE `id` = "'.$myid.'"');
							mysql_query('INSERT INTO `wd` (`user` ,`sum` ,`pm`,`in`,`purse`,`date` )VALUES ("'.$myid.'", "'.$_POST['amount'].'", "'.($_POST['sys']-1).'","0","'.$purse.'","'.time().'")');
							$wid=mysql_result(mysql_query('select `id` from `wd` where `user`="'.$myid.'" order by `id` desc limit 1'),0,'id');
	
							if($_POST['sys']==1)
							{
								$token=strtoupper(hash('SHA256',$lr_code.':'.date('Ymd',time()).':'.date('H',time())));							
								$zap='<TransferRequest%20id="'.rand(100000,999999).'"><Auth><ApiName>carmanapi</ApiName><Token>'.$token.'</Token></Auth><Transfer><TransferId>'.$wid.'</TransferId><TransferType>transfer</TransferType><Payer>'.$lr_purse.'</Payer><Payee>'.$arr['lr'].'</Payee><CurrencyId>LRUSD</CurrencyId><Amount>'.$_POST['amount'].'</Amount><Memo>Payment%20from%20vayrom.net</Memo><Anonymous>false</Anonymous></Transfer></TransferRequest>';
								$req='https://api.libertyreserve.com/xml/transfer.aspx?req='.$zap;
	        					$URL = $req;							
	        					$ch = curl_init();
	        					curl_setopt($ch, CURLOPT_URL, $URL);
	        					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	        					//curl_setopt($ch, CURLOPT_POST, 1);
	        					//curl_setopt($ch, CURLOPT_HEADER, 0);
	        					curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	        					$out=curl_exec($ch);
	        					curl_close($ch);
							}else{
								$f=@fopen('https://perfectmoney.com/acct/confirm.asp?AccountID='.$pm_user.'&PassPhrase='.$pm_pass.'&Payer_Account='.$pm_purse.'&Payee_Account='.$arr['pm'].'&Amount='.$_POST['amount'].'&PAY_IN=1&PAYMENT_ID='.rand(10000,99999), 'rb');
								$out=array(); $out="";
								while(!feof($f)) $out.=fgets($f);
								fclose($f);
							}
							mysql_query('update `wd` set `answer`="'.$out.'" where `wid`="'.$wid.'"');
							header('location:?m=account'); die('');
						}
					}					
				break;			
			
				case 'active':
					if($login==''){ break;}
					if(isset($_GET['del'])){
						$did=intval($_GET['del']);
						$zap=mysql_query('select `bet` from `depos` where `user_id`="'.$myid.'" and `dep_id`="'.$did.'"');
						if(mysql_num_rows($zap)>0){
							$arr=mysql_fetch_assoc($zap);
							$feed=$arr['bet']/2;
							mysql_query('update `users` set `amount`=(`amount`+"'.$feed.'") where `id`="'.$myid.'"');
							mysql_query('delete from `depos` where `dep_id`="'.$did.'"');
						}
					}	
				break;
			
				case 'ref':
					if($login==''){ break;}
					$count=mysql_result(mysql_query('select `proc` from `users` where `id`="'.$myid.'"'),0,'proc');
					$proc=5+$count*0.1;
					$zap=mysql_query('select `login` from `users` where `ref`="'.$myid.'"');
					$num=mysql_num_rows($zap);			
				break;

				case 'edit':
					if($login==''){break;}
					if(!empty($_POST['sub'])){ $e=0;
						$pass=mysql_result(mysql_query('select `pass` from `users` where `login`="'.$login.'"'),0,'pass');
						if($e==0) if(($pass!=$_POST['log'])&&(!empty($_POST['log']))){ $e++; $er[0]=I_PASS; }
						if($e==0) if((!empty($_POST['log']))and(((Iconv_strlen($_POST['pass'],'utf-8'))>20) or ((Iconv_strlen($_POST['pass'],'utf-8'))<4))){ $e++; $er[1]=PWD_LEN; }
						if($e==0) if($_POST['pass']!=$_POST['repass']){$er[1]=PWD_DIFF; $e++;}else{ if(!empty($_POST['log'])) $pass=$_POST['pass'];}
						if($e==0) if(empty($_POST['mail'])){$er[2]=E_MAIL; $e++;}else{$_POST['mail']=clr($_POST['mail']);
								$_POST['lr']=clr($_POST['lr']); $_POST['pm']=clr($_POST['pm']);}
						if($e==0) if(preg_match('/^[0-9a-zA-Z\._-]{1,50}[@]{1}[0-9a-z-]{1,25}[\.]{1}[a-z]{1,4}$/',$_POST['mail'])==0){ $e++; $er[2]=I_MAIL; }
						if($e==0) if((!empty($_POST['lr']))&&(preg_match('/^[UuXx]{1}[0-9]{3,10}$/',$_POST['lr'])==0)){ $e++; $er[3]=I_LR; }
						if($e==0) if((!empty($_POST['pm']))&&(preg_match('/^[UuXx]{1}[0-9]{3,10}$/',$_POST['pm'])==0)){ $e++; $er[4]=I_PM; }
						for($i=0;$i<5;$i++){if(!empty($er[$i])) $er[$i]='&nbsp;<font color="red">'.$er[$i].'</font>'; }
						
						if($e==0){
							mysql_query('UPDATE `users` SET `pass` = "'.$pass.'",`lr` = "'.$_POST['lr'].'",
							`pm` = "'.$_POST['pm'].'",`mail` = "'.$_POST['mail'].'" WHERE `id` = "'.$myid.'"');
							mail($_POST['mail'],'Notification from '.$domain,MAIL_INF,'from:faq@'.$domain);
							header('location:?m=account'); die('');
						}
					}else{ $zap=mysql_query('select `mail`,`pm`,`lr` from `users` where `login`="'.$login.'"'); 
						$arr=mysql_fetch_assoc($zap);
						$_POST['mail']=$arr['mail'];
						$_POST['pm']=$arr['pm'];
						$_POST['lr']=$arr['lr'];
					}	
				break;		
				
				case 'trans':
					$p=intval($_REQUEST['p']);
					$zz=mysql_query('select * from `wd` where `user`="'.$myid.'" order by `id` desc');
					$num=mysql_num_rows($zz);
					$pgs=ceil($num/$bypage);
					if(($p>$pgs)||($p<=0)){$p=1;}
					$fir=(($p-1)*$bypage);
					$zz=mysql_query('select * from `wd` where `user`="'.$myid.'" order by `id` desc limit '.$fir.','.$bypage);
					$num=mysql_num_rows($zz);
				break;
			
				default:

				break;	
			}
			
	@mysql_close($db);
	include('tmp.php');
?>