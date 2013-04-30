#!/bin/bash -
#
# 抓取并分析数据. 包括商品id, 现价, 原价, 月销售量, 评论书, 以及商家等.
#

export http_proxy="113.108.11.28:80"
test $# -eq 0 && urls="www.tmall.com" ||  urls="$*"
dt=$(date +%Y%m%d)
curl_flag=" -L -A Mozilla/5.0 -f -s  -m 10 -H Accept-Encoding:gzip,deflate "
for url in $urls ;do
	echo $url
	curl $curl_flag $url | gunzip | iconv -f cp936 -t utf-8 > tmp.html
	dos2unix tmp.html
	grep 'item.htm.id=' tmp.html | perl -pe 's/.*\?id.(\d+).*$/\1/g'| sort -u | while read product_id; do
		sed -n "/$product_id.*productImg/,/data-encode/p" tmp.html >  $product_id
		volume=$(grep -E '月销量|已售' $product_id | perl -pe 's/<\/em.*$//g' |  perl -pe 's/<em>//g' \
						| awk -F':' '{if ($0 ~ /万/) printf "%.0f\n", 10000*$2; else print $2}')
		flag1=$(echo $url | grep -c "sort=d")
		test $flag1 -eq 1 && test $volume -lt 100 && { rm -rf $product_id; continue; }
		flag2=$(echo $url | grep -c "sort=td")
		test $flag2 -eq 1 && test $volume -lt 1000 && { rm -rf $product_id; continue; }
		new_price=$(grep -A1  \"productPrice\" $product_id | tail -1 | perl -pe 's/<[^>]+>//g'| awk -F';' '{print $NF}' | perl -pe 's/ //g')
		test  -z "$new_price"  && { rm -rf $product_id; continue; }
		test `echo "$new_price<20" | bc` -eq 1 && { rm -rf $product_id; continue; }
		title=$(grep 'detail.tmall.com.*title=' $product_id | perl -pe 's/<[^>]+>//g')
		old_price=$(grep -A2  \"productPrice\" $product_id | tail -1 | grep -v 'productPrice' \
				| perl -pe 's/<[^>]+>//g'| awk -F';' '{print $NF}' | perl -pe  's/ //g')
		test -z "$old_price" && old_price=$(echo $new_price*1.0 | bc -l)
		comment=$(grep '累计评价' $product_id | perl -pe 's/.*累计评价//g ' |perl -pe 's/<[^>]+>//g'\
					        | awk -F':' '{if ($0 ~ /万/) printf "%.0f\n", 10000*$2; else print $2}')
		pic=$(grep taobaocdn $product_id | perl -pe 's/.*=.([^"]+).*$/\1/g' | grep taobaocdn | head -1 )
		shop=$( grep 'productShop-name' $product_id | perl -pe 's/<[^>]+>//g' )
		if [[ $flag1 -eq 1 ]];then
		 echo "set names utf8; INSERT INTO spider(product_id,new_price,old_price,volume,comment,pic,shop,title) \
			VALUES ('$product_id',$new_price,$old_price,$volume,$comment,'$pic','$shop','$title') \
			ON DUPLICATE KEY UPDATE new_price=$new_price, volume=$volume, comment=$comment, dt=now();" | mysql -hlocalhost -uroot -putaoinfo@2013  xiaopihai
		fi
		if [[ $flag2 -eq 1 ]];then
		 echo "set names utf8; INSERT INTO spider(product_id,new_price,old_price,total_volume,comment,pic,shop,title) \
			VALUES ('$product_id',$new_price,$old_price,$volume,$comment,'$pic','$shop','$title') \
			ON DUPLICATE KEY UPDATE new_price=$new_price, total_volume=$volume, comment=$comment, dt=now();" | mysql -hlocalhost -uroot -putaoinfo@2013  xiaopihai
		fi

		rm -rf $product_id
	done

done

rm -rf tmp.html
