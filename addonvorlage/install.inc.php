<?php
$dirnames = explode(DIRECTORY_SEPARATOR,dirname(__FILE__));
$mypage = $dirnames[count($dirnames)-1];
$REX['ADDON']['install'][$mypage] = 1;
?>