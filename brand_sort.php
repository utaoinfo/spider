<?php

$conn = mysql_connect('localhost','root','utaoinfo@2013');
mysql_query("SET NAMES UTF8");
mysql_select_db('xiaopihai', $conn);

$sql = "select brand_id, count(1) from xph_goods group by brand_id ";
var_dump($sql);
$result_all = mysql_query($sql, $conn);
while($row=mysql_fetch_array($result_all)) {
	$sql = "update xph_brand set sort=100-$row[1] where id = $row[0]";
	$result = mysql_query($sql, $conn);
	if(!$result){
		echo "Error: " . mysql_error();
	}
}

mysql_close($conn);
