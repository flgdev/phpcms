<?php
header("Content-type:image/gif");
session_start();
$_SESSION['code']=rand(10000,99999);
error_reporting(E_ALL);
$pic = imageCreateFromgif("image.gif");
imagestring($pic,5,8,3,$_SESSION['code'],614656);
imagegif($pic);
imagedestroy($pic);
?>