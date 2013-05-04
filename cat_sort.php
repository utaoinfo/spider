<?php

$conn = mysql_connect('localhost','root','utaoinfo@2013');
mysql_query("SET NAMES UTF8");
mysql_select_db('xiaopihai', $conn);

$sql = "select category_id,count(1) from xph_category_extend group by category_id ";
var_dump($sql);
$result_all = mysql_query($sql, $conn);
while($row=mysql_fetch_array($result_all)) {
	$sql = "update xph_category set sort = 2000-$row[1] where id=$row[0]";
	$result = mysql_query($sql, $conn);
	if(!$result){
		echo  $sql . " Error: " . mysql_error();
	}
}

$sql = "select id from xph_category where parent_id>=1 && parent_id<10"; 
var_dump($sql);
$result_all = mysql_query($sql, $conn);
while($row=mysql_fetch_array($result_all)) {
	$sql = "select sum(2000-sort) from xph_category where parent_id=$row[0]";
	$result = mysql_query($sql, $conn);
	while($row1=mysql_fetch_array($result)) {
		$sql = "update xph_category set sort=2000-$row1[0] where id=$row[0]";
		mysql_query($sql, $conn);
	}
}

$sql = "select id from xph_category where parent_id=0"; 
var_dump($sql);
$result_all = mysql_query($sql, $conn);
while($row=mysql_fetch_array($result_all)) {
	$sql = "select sum(2000-sort) from xph_category where parent_id=$row[0]";
	$result = mysql_query($sql, $conn);
	while($row1=mysql_fetch_array($result)) {
		$sql = "update xph_category set sort=2000-$row1[0] where id=$row[0]";
		mysql_query($sql, $conn);
	}
}

mysql_close($conn);
