<?php

include "taobaoapi/TopSdk.php";

function getItemInfo($item_id){
	$c = new TopClient;
	$c->appkey = '21483968';
	$c->secretKey = '36d68b83e6d0782910519d0dc9b5e76f';
	$req = new ItemGetRequest;
	$req->setFields("num_iid,cid,props_name");
	$req->setNumIid($item_id);
	$resp = $c->execute($req);
	return $resp;
}


$conn = mysql_connect('localhost','root','utaoinfo@2013');
mysql_query("SET NAMES UTF8");
mysql_select_db('xiaopihai', $conn);
$sql = "select goods_no,id from xph_goods";
$result_all = mysql_query($sql, $conn);
while($row=mysql_fetch_array($result_all)) {
	var_dump($row[0]);
	$item = getItemInfo($row[0], 0);
	$content=$item->item->props_name;
	$pattern = '/\d+:\d+:/i';
	$replacement = '';
	$content=preg_replace($pattern, $replacement, $content);
	$sql = "update xph_goods set content='$content' where goods_no = '$row[0]'";
	mysql_query($sql, $conn);

	$goods_id=$row[1];
	$category_id=$item->item->cid;
	$sql="select * from xph_category_extend where goods_id = '$goods_id'";
	$result = mysql_query($sql, $conn);
	$rows =mysql_affected_rows($conn);
	if ( $rows == 0){
		$sql = "insert into xph_category_extend(goods_id,category_id) values('$goods_id', $category_id)";
		$result = mysql_query($sql, $conn);
		if(!$result){
			var_dump($sql ." Error: " . mysql_error());
		}

	} else {
		$sql = "update xph_category_extend set category_id=$category_id where goods_id = '$goods_id'";
		$result = mysql_query($sql, $conn);
		if(!$result){
			var_dump($sql ." Error: " . mysql_error());
		}

	}
}

mysql_close($conn);
