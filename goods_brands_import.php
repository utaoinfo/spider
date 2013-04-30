<?php

include "taobaoapi/TopSdk.php";

function searchItem($keyword, $cid){
	$c = new TopClient;
	$c->appkey = '21485508';
	$c->secretKey = 'bd309059d260a65a1439b587ae0edb78';

	$req = new TaobaokeItemsGetRequest;
	$req->setFields("num_iid,title,nick,pic_url,price,click_url,commission,commission_rate,commission_num,commission_volume,shop_click_url,seller_credit_score,item_location,volume");
	$req->setPid(41861233);
	$req->setNick($keyword);
	$req->setKeyword($keyword);
	$req->setSort("commissionVolume_desc");
	$req->setGuarantee("true");
	$req->setStartCommissionRate("500");
	$req->setEndCommissionRate("5000");
	$req->setStartCommissionNum("10");
	$req->setEndCommissionNum("5000");
	$req->setStartPrice("20");
	$req->setEndPrice("1000");
	$req->setStartTotalnum("100");
	$req->setEndTotalnum("50000");
	$req->setMallItem("true");
	$req->setPageNo(1);
	$req->setPageSize(20);
	$req->setOuterCode("abc");
	$resp = $c->execute($req);
	return $resp;
}

$conn = mysql_connect('localhost','root','utaoinfo@2013');
mysql_query("SET NAMES UTF8"); 
mysql_select_db('xiaopihai', $conn);

$taobao_items = searchItem($argv[1], 0);
if(!$taobao_items->taobaoke_items) {
	exit(0);
}

$sellernick = '';
foreach($taobao_items->taobaoke_items->taobaoke_item as $item){
	$volume = $item->volume;
	$commission = $item->commission;
	if ($commission < 1){
		continue;
	}
	$price = $item->price;
	$name=htmlspecialchars(strip_tags($item->title),ENT_QUOTES);
	$goods_no = $item->num_iid;
	$img = $item->pic_url;
	$url = $item->click_url;
	$sellernick = $item->nick;
	$price = $item->price;
	$create_time = gmdate("Y/m/d H:i:s");
	$sql="select * from xph_goods where goods_no = '$goods_no'";
	$result = mysql_query($sql, $conn);
	$rows =mysql_affected_rows($conn);
	if ( $rows == 0){
		$sql = "insert into xph_goods(goods_no,name,img,url,commission,volume,sellernick,sell_price, create_time) values('$goods_no','$name','$img','$url', $commission,$volume,'$sellernick',$price,'$create_time')"; 
		$result = mysql_query($sql, $conn);
		if(!$result){
			var_dump($sql ." Error: " . mysql_error());
		}
	} else {
		$sql = "update xph_goods set commission=$commission,volume=$volume,url='$url',sell_price=$price,create_time='$create_time' where goods_no='$goods_no'";
		$result = mysql_query($sql, $conn);
		if(!$result){
			var_dump($sql ." Error: " . mysql_error());
		}
	}

	$shop_url= $item->shop_click_url;
	$cid = $item->cid;
	$sql="select * from xph_brand where  name='$sellernick'";
	$result = mysql_query($sql, $conn);
	$rows =mysql_affected_rows($conn);
	if ( $rows == 0){
		$sql = "insert into xph_brand(name,url,category_ids) values('$sellernick','$shop_url',$cid)"; 
		$result = mysql_query($sql, $conn);
		if(!$result){
			var_dump($sql ." Error: " . mysql_error());
		}
	}else{
		$sql = "update xph_brand set url='$shop_url' where name='$sellernick'";
		$result = mysql_query($sql, $conn);
		if(!$result){
			var_dump($sql ." Error: " . mysql_error());
		}
	}
}
