<?php
$file = $_GET['image_path']."&data=".$_GET['data'];
header("Pragma: public");
header("Expires: 0");
header("Content-Type: application/force-download");
header( "Content-Disposition: attachment; filename=".$_GET['website'].".png");
header( "Content-Description: File Transfer");
@readfile($file);
?>