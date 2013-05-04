<?php

include "taobaoapi/TopSdk.php";

function searchItem($keyword, $cid){
	$c = new TopClient;
	$c->appkey = '21485508';
	$c->secretKey = 'bd309059d260a65a1439b587ae0edb78';

	$req = new TaobaokeItemsGetRequest;
	$req->setFields("num_iid,title,nick,pic_url,price,click_url,commission,commission_num,commission_volume,shop_click_url,seller_credit_score,item_location,volume");
	$req->setPid(41861233);
	$req->setNick($keyword);
	$req->setKeyword($keyword);
	/**
	 * 默认排序:default
	 price_desc(价格从高到低)
	 price_asc(价格从低到高)
	 credit_desc(信用等级从高到低)
	 commissionRate_desc(佣金比率从高到低)
	 commissionRate_asc(佣金比率从低到高)
	 commissionNum_desc(成交量成高到低)
	 commissionNum_asc(成交量从低到高)
	 commissionVolume_desc(总支出佣金从高到低)
	 commissionVolume_asc(总支出佣金从低到高)
	 delistTime_desc(商品下架时间从高到低)
	 delistTime_asc(商品下架时间从低到高)
	 **/

	$req->setSort("commissionVolume_desc");
	$req->setGuarantee("true");
	$req->setStartCommissionRate("500");
	$req->setEndCommissionRate("5000");
	$req->setStartCommissionNum("10");
	$req->setEndCommissionNum("5000");
	$req->setStartPrice("20");
	$req->setEndPrice("5000");
	$req->setStartTotalnum("100");
	$req->setEndTotalnum("50000");
	$req->setMallItem("true");
	$req->setPageNo(1);
	$req->setPageSize(40);
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
	$name=htmlspecialchars(strip_tags($item->title),ENT_QUOTES);
	$goods_no = $item->num_iid;
	$img = $item->pic_url;
	$url = $item->click_url;
	$names = array("官方旗舰店", "童鞋旗舰店", "旗舰店");
	$sellernick = str_replace($names, "", $item->nick);
	$price = $item->price;
	if($price * $volume < 20000) {
		continue;
	}
	$create_time = gmdate("Y/m/d H:i:s");
	$sql="select * from xph_goods where goods_no = '$goods_no'";
	$result = mysql_query($sql, $conn);
	$rows =mysql_affected_rows($conn);
	if ( $rows == 0){
		$sql = "insert into xph_goods(goods_no,name,img,url,commission,volume,sellernick,market_price, create_time) values('$goods_no','$name','$img','$url', $commission,$volume,'$sellernick',$price,'$create_time')"; 
		$result = mysql_query($sql, $conn);
		if(!$result){
			var_dump($sql ." Error: " . mysql_error());
		}
	} else {
		$sql = "update xph_goods set commission=$commission,volume=$volume,url='$url',market_price=$price,create_time='$create_time' where goods_no='$goods_no'";
		$result = mysql_query($sql, $conn);
		if(!$result){
			var_dump($sql ." Error: " . mysql_error());
		}
	}

	$shop_url= $item->shop_click_url;
	$sql="select * from xph_brand where  name='$sellernick'";
	$result = mysql_query($sql, $conn);
	$rows =mysql_affected_rows($conn);
	if ( $rows == 0){
		$sql = "insert into xph_brand(name,url) values('$sellernick','$shop_url')"; 
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
