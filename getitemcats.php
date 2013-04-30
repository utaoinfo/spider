<?php
include "taobaoapi/TopSdk.php";

function getItemCatInfo($cid){
	$c = new TopClient;
	$c->appkey = '21485508';
	$c->secretKey = 'bd309059d260a65a1439b587ae0edb78';
	$req = new ItemcatsGetRequest;
	$req->setFields("cid,parent_cid,name,is_parent");
	$req->setcids($cid);
	$resp = $c->execute($req);
	return $resp;
}

$conn = mysql_connect('localhost','root','utaoinfo@2013');
mysql_query("SET NAMES UTF8");
mysql_select_db('xiaopihai', $conn);

$sql = "select distinct parent_id from xph_category where parent_id>1";
var_dump($sql);
$result_all = mysql_query($sql, $conn);
while($row=mysql_fetch_array($result_all)) {
	$item = getItemCatInfo($row[0], 0);
	$item_cat=$item->item_cats->item_cat;
	if(!$item_cat) {
		continue;
	}
	$name=$item_cat->name;
	$cid=$item_cat->cid;
	$parent_cid=$item_cat->parent_cid;
	$is_parent=$item_cat->is_parent;
	if ($is_parent == "true"){
		$parent_cid= -1;
	}
	$sql = "insert into xph_category(id,parent_id,name) values($cid, $parent_cid, '$name') on duplicate key update parent_id=$parent_cid, name='$name'";
	$result = mysql_query($sql, $conn);
	if(!$result){
		echo "Error: " . mysql_error();
	}
}

exit(0);
$sql = "select distinct category_id from xph_category_extend";
var_dump($sql);
$result_all = mysql_query($sql, $conn);
while($row=mysql_fetch_array($result_all)) {
	$item = getItemCatInfo($row[0], 0);
	$item_cat=$item->item_cats->item_cat;
	if(!$item_cat) {
		continue;
	}
	$name=$item_cat->name;
	$cid=$item_cat->cid;
	$parent_cid=$item_cat->parent_cid;
	$is_parent=$item_cat->is_parent;
	if ($is_parent == "true"){
		$parent_cid= -1;
	}
	$sql = "insert into xph_category(id,parent_id,name) values($cid, $parent_cid, '$name') on duplicate key update parent_id=$parent_cid, name='$name'";
	$result = mysql_query($sql, $conn);
	if(!$result){
		echo "Error: " . mysql_error();
	}
}

mysql_close($conn);
