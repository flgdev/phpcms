<?php
function auth(){ global $page; $page='<center>'.AUTH_PLEASE.'</center>';}

switch($m){
	case 'in':
	
	break;
	
	case 'forget':
		if((empty($_POST['login']))||($auth<1))
			$page.='
			<h1>'.FP.'</h1><br />';
			if($err!=''){$page.='
			<font color="red">'.$err.'</font><br />';}
		$page.='
		<form action="?" method="post">
		<table> 
		<tr><td align="right">'.USERNAME.' </td><td><input type="text" name="login" value="'.$login.'"></td></tr>
		<tr><td align="right">'.MAIL.' </td><td><input type="text" name="mail" value="'.$mail.'"></td></tr>
		<tr><td align="right">'.CAPTCHA.': <img src="image.php" /></td><td><input type="text" name="code" maxlength="5" /></td></tr>
		<input type="hidden" name="m" value="forget">
		<tr><td></td><td align="right"><input type="submit" name="sub" value="'.RESTORE.'" /></td></tr>
		</table>
		</form>';
	break;
	
	case 'reg':
		$page='		
		<table>
		<form action="?" method="post">	 					
		<tr><td align="right">'.USERNAME.':</td><td><input type="text" name="log" value="'.$_POST['log'].'" size="22" maxlength="50"  />'.$er[0].'</td></tr>
		<tr><td align="right">'.PASSWORD.':</td><td><input type="text" name="pass" value="'.$_POST['pass'].'" size="22" maxlength="50" />'.$er[1].'</td></tr>
		<tr><td align="right">'.RE_PASS.':</td><td><input type="text" name="repass" value="'.$_POST['repass'].'" size="22" maxlength="50" /></td></tr>
		<tr><td align="right">'.MAIL.':</td><td><input type="text" name="mail" value="'.$_POST['mail'].'" size="22" maxlength="100" />'.$er[2].'</td></tr>
		<tr><td align="right">'.LR.':</td><td><input type="text" name="lr" value="'.$_POST['lr'].'" maxlength="9" size="22" />'.$er[3].'</td></tr>
		<tr><td align="right">'.PM.':</td><td><input type="text" name="pm" value="'.$_POST['pm'].'" maxlength="9" size="22" />'.$er[4].'</td></tr>
		<tr><td align="right">'.REFERRER.':</td><td><input type="text" name="ref" value="'.@trim($_COOKIE['ref']).'" size="22" disabled /></td></tr>
		<tr><td align="right">'.CAPTCHA.': <img src="image.php" /></td><td><input type="text" name="code" maxlength="5" size="22" />'.$er[5].'</td></tr>
		<tr><td></td><td align="right"><input type="checkbox" name="agree" checked disabled /> '.I_AGREE.'</td></tr>
		<input type="hidden" name="m" value="reg" />
		<tr><td></td><td align="right"><input type="submit" name="sub" value="'.RA.'" /></td></tr>
		</form>
		</table>';	
		$ttl=REG;
	break;
	
	case 'account':
		if($login==''){auth(); break;}
		$arr['reinv']='
		<form method="post" style="display:inline;">
		'.REINV.' <select name="reinv" value="0">
		<option value="0" '.$rsel[0].'>0%</option>
		<option value="20" '.$rsel[20].'>20%</option>
		<option value="40" '.$rsel[40].'>40%</option>
		<option value="60" '.$rsel[60].'>60%</option>
		<option value="80" '.$rsel[80].'>80%</option>
		<option value="100" '.$rsel[100].'>100%</option>
		</select>
		<input type="hidden" name="m" value="account">
		<input type="submit" value="OK" name="csub">
		</form>';
		$page.='
		'.BALANCE.' - '.sround(2,$arr['amount']).'$<br />
		'.DEPOSITED.' - '.sround(2,$dsum).'$<br />
		'.WITHDRAWED.' - '.sround(2,$wsum).'$<br />';
		$page.='
		'.$arr['reinv'].'<br />';
		$page.='
		'.REFERALS.' - '.$ref.'<br />';
		$ttl=ACCOUNT_OVERVIEW;
	break;
	
	case 'deposit':
		if($login==''){auth(); break;}
		if((empty($_POST['sub']))||($err!='')){
		$page=
		'<form action="?" method="post">
		<table> 	 					
		<tr><td align="right">'.ACC_BAL.'</td><td>'.$arr['amount'].'$</td></tr>
		<tr><td align="right">'.AMOUNT.'</td><td><input type="text" name="amount" /></td>'.$err.'</tr>
		<tr><td align="right">'.PAY_WITH.'</td><td>
			<select name="sys">
			<option value="0">'.ACC_BAL.'</option>
			<option value="1">'.LR.'</option>
			<option value="2">'.PM.'</option>
			</select>					
		</td></tr>
		<tr><td align="right">'.ucfirst(PLAN).'</td><td>
			<select name="pl">
			<option value="0">'.P1.'</option>
			<option value="1">'.P2.'</option>
			<option value="2">'.P3.'</option>
			<option value="3">'.P4.'</option>
			</select>					
		</td></tr>
		<tr><td></td><td align="right"><input type="submit" name="sub" value="'.CREATE_DEP.'" /></td></tr>
		<input type="hidden" name="m" value="deposit">
		</table>
		</form>';
					}
		$ttl=DEPOSIT_FUNDS;
	break;
	
	case 'withdraw':
		if($login==''){auth(); break;}
		$page='
		<form action="?" method="post">
		<table border="0" cellspacing="5"> 	 
			<tr><td align="right">'.ACC_BAL.'</td><td>'.sround(2,$arr['money']).'$</td></tr>
			<tr><td align="right">'.AMOUNT.'</td><td><input type="text" name="amount" value="'.$_POST['amount'].'" /></td>'.$err.'</tr>
			<tr><td align="right">'.PAY_SYS.'</td><td><select name="sys">
			<option value="1" selected>'.LR.'</option>
			<option value="2" >'.PM.'</option>
			</select></td></tr>
			<input type="hidden" name="m" value="withdraw">
			<tr><td></td><td align="right"><input type="submit" name="sub" class="button" value="'.DO_WITHDRAW.'" /></tr>
		</table>
		</form>';
		$ttl=WITHDRAW_FUNDS;
	break;
	
	case 'active':
		if($login==''){auth(); break;}
		$ttl=ACTIVE_DEPOSITS;
		$zap=mysql_query('select `sum` from `wd` where (`in`="0")and(`user`="'.$myid.'")');
		$num=mysql_num_rows($zap); $wsum=0;
		for($i=0;$i<$num;$i++){ $ar=mysql_fetch_assoc($zap); $wsum+=$ar['sum']; }
		$zap=mysql_query('select * from `depos` where (`user_id`="'.$myid.'")');
		$num=mysql_num_rows($zap);
		if($num>0)
		{
		$page.='
		<table border="1px" cellspacing="0" cellpadding="0" style="width:100%">
			<tr class="row1"><td>'.AMOUNT.'</td><td>'.DAYS1.'</td><td>'.PER.'</td><td>'.DAILY1.'</td><td>'.DEL_GB.'</td></tr>';
			while(1){
				$arr=mysql_fetch_assoc($zap);
				if(empty($arr['bet']))break;
				$feed=sround(2,$arr['bet']/2);
				if($feed>0){$jst='return confirm(\''.SURE.'\');" href="?m=active&del='.$arr['dep_id'];}else{$jst='alert(\'Comission is 100%, you cant get back your deposit now!\')';}
				$page.='
				<tr class="row2"><td>'.sround(2,$arr['bet']).'$</td><td>'.$arr['paid'].'</td>
				<td>'.($arr['proc']/10).'%</td><td>'.sround(3,($arr['bet']*($arr['proc']/10)/100)).'$</td><td><a onclick="'.$jst.'"><font color="blue">Get '.$feed.'$ (50%)</font></a></td></tr>';
			}
		}else{
			$page.='
			<tr>'.HAVENT_DEP.'</tr>';
		}
		$page.='
		</table>
		';				
	break;
	
	case 'ref':
		if($login==''){auth(); break;}
		$page.=REF_LINK.' <input type="text" size="50" value="http://'.$domain.'/?l='.$login.'"><br />
		<br />
		<table border="1px" cellspacing="0" cellpadding="0" style="width:100%">
		<tr class="row1"><td>'.USERNAME.'</td><td>'.YOUR_PROF.'</td></tr>';
		$a2=0;
		for($i=0;$i<$num;$i++)
		{
			$arr=mysql_fetch_assoc($zap);
			$log=$arr['login'];
			$za=mysql_query('select `toref` from `users` where `login`="'.$log.'"');
			$toref=mysql_result($za,0,'toref');
			$a2+=$toref;
		$page.='
		<tr class="row2"><td>'.$log.'</td><td>'.sround(2,$toref).'$</td></tr>';							
		}
		$page.='
		<tr class="row3"><td><b>'.TOTAL.'</b></td><td><b>'.sround(2,$a2).'$</b></td></tr>
		</table>';		
		$ttl=REFERALS;
	break;
	
	case 'edit':
		if($login==''){auth(); break;}
		$page=E_INF_A.'<br /><br />
		<form action="?" method="post"> 	
		<table> 					
		<tr><td align="right">'.PASSWORD.': </td><td><input type="text" name="log" value="'.$_POST['log'].'" maxlength="50" /></td>'.$er[0].'</tr>
		<tr><td align="right">'.NPASS.': </td><td><input type="text" name="pass" value="'.$_POST['pass'].'" maxlength="50" /></td>'.$er[1].'</tr>
		<tr><td align="right">'.RE_PASS.': </td><td><input type="text" name="repass" value="'.$_POST['repass'].'" maxlength="50" /></td></tr>
		<tr><td align="right">'.MAIL.': </td><td><input type="text" name="mail" value="'.$_POST['mail'].'" maxlength="100" /></td>'.$er[2].'</tr>
		<tr><td align="right">'.LR.': </td><td><input type="text" name="lr" value="'.$_POST['lr'].'" maxlength="9" /></td>'.$er[3].'</tr>
		<tr><td align="right">'.PM.': </td><td><input type="text" name="pm" value="'.$_POST['pm'].'" maxlength="9" /></td>'.$er[4].'</tr>
		<input type="hidden" name="m" value="edit" />
		<tr><td></td><td align="right"><input type="submit" name="sub" value="'.EDIT.'" /></td></tr>
		</table>
		</form>';		
		$ttl=EDIT_PROFILE;
	break;
	
	case 'trans':
		if($login==''){auth(); break;}
		$page.='
		<h1>'.TRANS.'</h1><br />
		<table border="1px" cellspacing="0" cellpadding="0" style="width:100%;font-size:15px;">
			<tr class="row1"><td>'.TID.'</td><td>'.AMOUNT.'</td><td>'.PAY_SYS.'</td><td>'.TYPE.'</td><td>'.PURSE.'</td><td>'.DATE.'</td></tr>';
			for($i=0;$i<$num;$i++)
			{
				$arr=mysql_fetch_assoc($zz);
				if($arr['in']){$type=DEPOSIT;}else{$type=WITHDRAW;}
				if($arr['pm']){$sys=PM;}else{$sys=LR;}
				$page.='
			<tr class="row2"><td>'.$arr['id'].'</td><td>'.$arr['sum'].'$</td><td>'.$sys.'</td><td>'.$type.'</td><td>'.$arr['purse'].'</td><td>'.date('m-d H:i',$arr['date']).'</td></tr>';
			}
		$page.='
		</table><h1><center><br />';
		for($i=1;$i<=$pgs;$i++){
			if($i!=1) $page.=' |';
			if($p==$i){
				$page.=' '.$i;
			}else{
				$page.=' <a href="?m=trans&p='.$i.'">'.$i.'</a>';
			}
		}
		$page.='</center></h1>';
	break;
	
	case 'banner':
		function txt($size){
			global $domain;
			global $login;
			return '<img src="images/'.$size.'.gif" /><br /><textarea cols="70" rows="3">'.htmlspecialchars('<a href="http://'.$domain.'/?l='.$login.'"><img src="http://'.$domain.'/images/'.$size.'.gif" title="http://'.$domain.'" alt="http://'.$domain.'"></a>').'</textarea><br /><br />';
		}
		$page=txt('468x60').txt('728x90').txt('350x19').txt('125x125').txt('160x600');	
	break;
	
	case 'about': $page=ABOUT_A; $ttl=ABOUT; break;
	case 'faq': $page=nl2br(FAQ_A); $ttl=FAQ; break;
	case 'support': $page=nl2br(SUPPORT_A); $ttl=SUPPORT; break;
	case 'out': $page=LOGOUT_A; break;
	case 'terms': $page=nl2br(RULES_A); $ttl=RULES; break;
	case 'news': $page=NEWS_A; $ttl=NEWS; break;
	case 'rep': $page=REPORTS_A; $ttl=REPORTS; break;
	case 'mon': $page=MONITORING_A; $ttl=MONITORING; break;
	default: $page=MAIN_A; $ttl=$sitename; break;
}

if(($a!=1)||($_REQUEST['m']=='out')){
	$litem='<li><a href="?m=reg">'.REG.'</a></li>';
	$lbar='
				<div class="box">
					<div class="top-left">
						<div class="top-right"></div>
					</div>
					<div class="clear"></div>
					<p class="ml">'.AUTH.'</p>
					<div class="login_w">
						<form id="loginform" action="?" method="post">
						<p class="label" style="padding-top:10px;">
							<label>'.USERNAME.':</label>
						</p>
						<input type="hidden" name="m" value="in">
						<p><input type="text" name="login" class="login_f" value="'.USERNAME.'" onfocus="if (this.value == \''.USERNAME.'\') {this.value = \'\';}" 
						onblur="if (this.value == \'\') {this.value = \''.USERNAME.'\';}"><img src="images/lock.gif" alt="" class="lock"></p>
						<div class="clear"></div>
						<p class="label"><label>'.PASSWORD.':</label></p>
						<p><input type="text" name="pass" class="pass_f" value="'.PASSWORD.'" onfocus="if (this.value == \''.PASSWORD.'\' & this.type == \'text\')
						{this.value = \'\';this.type = \''.PASSWORD.'\';}" onblur="if (this.value == \'\' & this.type == \''.PASSWORD.'\') 
						{this.value = \''.PASSWORD.'\';this.type = \'text\';}"> <input type="submit" name="submit" value="Login" class="login_b"></p>
						</form>
						<p class="for_reg"><a href="?m=forget">'.FP.'?</a><br>
						<a href="?m=reg">'.RA.'</a></p>
					</div>
					<div class="clear"></div>
					<div class="bottom-left">
						<div class="bottom-right"></div>
					</div>
				</div>
				<div class="clear"></div>
				<div class="referal">
					<p><span><strong>7%</strong></span><br>
					'.RC.'</p>
				</div>
';}else{
	$litem='<li><a href="?m=out">'.OUT.'</a></li>';
	$lbar='
				<br /><div class="right_block bot">
					<p><a href="?m=account">'.ACCOUNT_OVERVIEW.'</a></p>
				</div>

				<div class="right_block bot">
					<p><a href="?m=deposit">'.DEPOSIT_FUNDS.'</a></p>
				</div>

				<div class="right_block bot">
					<p><a href="?m=active">'.ACTIVE_DEPOSITS.'</a></p>
				</div>
				
				<div class="right_block bot">
					<p><a href="?m=withdraw">'.WITHDRAW_FUNDS.'</a></p>
				</div>	

				<div class="right_block bot">
					<p><a href="?m=trans">'.TRANS.'</a></p>
				</div>	

				<div class="right_block bot">
					<p><a href="?m=ref">'.REFERALS.'</a></p>
				</div>	

				<div class="right_block bot">
					<p><a href="?m=edit">'.EDIT_PROFILE.'</a></p>
				</div>
';
}

echo
'<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<title>'.$sitename.TITLE.'</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="stylesheet" type="text/css" href="css/style.css">

		<!--[if lte IE 6]>
		<link rel="stylesheet" type="text/css" href="css/ie6.css"/>
		<![endif]-->

		<!--[if lte IE 7]>
		<link rel="stylesheet" type="text/css" href="css/ie7.css"/>
		<![endif]-->
		
		<script language="JavaScript"> 
		 numimg=0; 
		 imgslide=new Array(); 
		 imgslide[0]=new Image(); imgslide[1]=new Image(); imgslide[2]=new Image();
		 imgslide[0].src="images/screen0.jpg"; imgslide[1].src="images/screen1.jpg"; imgslide[2].src="images/screen2.jpg";
		 function demoslides()
		 {
			document.getElementById(\'slide\').src=imgslide[numimg].src;
			numimg++; if(numimg==3) numimg=0; 
			setTimeout("demoslides()", 3000);
		 } 
		 </script> 

	</head>
	<body onLoad="demoslides()">
		<ul id="flags">
			<li><a href="#" onClick="document.cookie = \'lang=ru\';  location.reload(true);"><img src="images/rus_flag.png" alt=""></a></li>
			<li><a href="#" onClick="document.cookie = \'lang=sp\';  location.reload(true);"><img src="images/spa_flag.png" alt=""></a></li>
			<li><a href="#" onClick="document.cookie = \'lang=ge\';  location.reload(true);"><img src="images/ger_flag.png" alt=""></a></li>
			<li><a href="#" onClick="document.cookie = \'lang=en\';  location.reload(true);"><img src="images/gb_flag.png" alt=""></a></li>
		</ul>

		<div id="header">
			<div class="header">
				<img src="images/logo.png" alt="" id="logo">
				<ul id="topmenu">
					<li><a href="?h">'.HOME.'</a></li>
					<li><a href="?m=news">'.NEWS.'</a></li>
					<li><a href="?m=faq">'.FAQ.'</a></li>
					<li><a href="?m=mon">'.MONITORING.'</a></li>
					<li><a href="?m=banner">'.BANNERS.'</a></li>
					<li><a href="?m=support">'.SUPPORT.'</a></li>
					<li><a href="?m=rep">'.REPORTS.'</a></li>
					'.$litem.'
				</ul>
			</div>
		
			<div id="header_line"></div>
		</div>
	
		<div id="container">
			<div id="l_sidebar">
				'.$lbar.'
			</div>
			<div id="content">
				<img src="images/screen0.jpg" alt="" id="slide" class="slide">
				<ul class="plans">
					<li>
						<div class="plan">
							<h2><span>&nbsp;</span>'.P1.' '.PLAN.'</h2>
							<div class="red">1.5% '.DAILY.'</div>
							<div class="green">'.FR.' 20 '.DAYS.'</div>
							<p class="min_max">min: <span class="min">$1</span>|max: unlimited<br>
							Principal return:<span class="return">'.YES.'</span></p>
						</div>
						<div class="plan_shadow">
						</div>
					</li>
					<li>
						<div class="plan">
							<h2><span>&nbsp;</span>'.P2.' '.PLAN.'</h2>
							<div class="red">1.7% '.DAILY.'</div>
							<div class="green">'.FR.' 30 '.DAYS.'</div>
							<p class="min_max">min: <span class="min">$10</span>|max: unlimited<br>
							Principal return:<span class="return">'.YES.'</span></p>
						</div>
						<div class="plan_shadow">
						</div>
					</li>
					<li>
						<div class="plan">
							<h2><span>&nbsp;</span>'.P3.' '.PLAN.'</h2>
							<div class="red">1.9% '.DAILY.'</div>
							<div class="green">'.FR.' 40 '.DAYS.'</div>
							<p class="min_max">min: <span class="min">$100</span>|max: unlimited<br>
							Principal return:<span class="return">'.YES.'</span></p>
						</div>
						<div class="plan_shadow">
						</div>
					</li>
					<li>
						<div class="plan">
							<h2><span>&nbsp;</span>'.P4.' '.PLAN.'</h2>
							<div class="red">2.2% '.DAILY.'</div>
							<div class="green">'.FR.' 60 '.DAYS.'</div>
							<p class="min_max">min: <span class="min">$500</span>|max: unlimited<br>
							Principal return:<span class="return">'.YES.'</span></p>
						</div>
						<div class="plan_shadow">
						</div>
					</li>
				</ul>
			</div>
			
			<div id="r_sidebar">
				<div class="right_block">
					<img src="images/shield.png" class="icon" alt="">
					<p>DDoS &amp; SSl <br> Protection</p>
				</div>

				<div class="right_block">
					<img src="images/we_accept.gif" class="icon_we" alt="">
					<p class="we_accept">We accept</p>
				</div>
				
				<div class="liberty">
					<img src="images/liberty.gif" alt="">
					<img src="images/perfect.gif" alt="" style="margin-top:0px;" >
				</div>

				<div class="right_block bot">
					<img src="images/icon_p.gif" alt="">
					<p>Stable</p>
				</div>	

				<div class="right_block bot">
					<img src="images/icon_p.gif" alt="">
					<p>Profitable</p>
				</div>	

				<div class="right_block bot">
					<img src="images/icon_p.gif" alt="">
					<p>Comfortable</p>
				</div>
			</div>
			<div class="clear"></div>
		</div>
	
		<div id="footer">
			<div id="footer_line">
				<h1>'.$ttl.'</h1>
				<br />
				'.$page.'
			</div>
		</div>

		<div id="footer1_line">
			<div id="footer1">
				<div class="cont">
					<p class="copyright">Copyright &copy; 2011. Footballinvest, All Rights Reserved</p>
					<div class="banners">
						<a href="#"><img src="images/dragonara.png" alt=""></a> <a href="#"><img src="images/comodo.png" alt=""></a>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>';
?>