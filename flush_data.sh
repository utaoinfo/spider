#!/bin/bash -x

echo "update xph_goods,xph_spider set xph_goods.market_price=xph_spider.old_price  where xph_goods.goods_no=xph_spider.product_id"  | mysql -hlocalhost -uroot -putaoinfo@2013 xiaopihai
echo "update xph_goods,xph_spider set xph_goods.sell_price=xph_spider.new_price  where xph_goods.goods_no=xph_spider.product_id"  | mysql -hlocalhost -uroot -putaoinfo@2013 xiaopihai
echo "update xph_goods,xph_spider set xph_goods.total_volume=xph_spider.total_volume where xph_goods.goods_no=xph_spider.product_id"  | mysql -hlocalhost -uroot -putaoinfo@2013 xiaopihai

echo "update xph_goods,xph_spider set xph_goods.total_volume=xph_spider.volume where xph_goods.goods_no=xph_spider.product_id and xph_goods.total_volume=0"  | mysql -hlocalhost -uroot -putaoinfo@2013 xiaopihai

echo "update xph_goods,xph_spider set xph_goods.list_img=replace(pic,'_160x160','_220x220'), xph_goods.small_img=replace(pic,'_160x160','_80x80') where xph_goods.goods_no=xph_spider.product_id " | mysql -hlocalhost -uroot -putaoinfo@2013  xiaopihai

echo "delete from xph_goods where brand_id in (select id from xph_brand where credit_score<=10) " | mysql -hlocalhost -uroot -putaoinfo@2013 xiaopihai
echo "delete from xph_brand where credit_score<=10" | mysql -hlocalhost -uroot -putaoinfo@2013 xiaopihai
echo "delete from xph_category_extend  where goods_id not in (select id from xph_goods)" | mysql -hlocalhost -uroot -putaoinfo@2013 xiaopihai

#echo " delete from xph_goods where market_price=sell_price" |  mysql -hlocalhost -uroot -putaoinfo@2013 xiaopihai
echo " delete from xph_goods where sell_price < 0.1" |  mysql -hlocalhost -uroot -putaoinfo@2013 xiaopihai

echo "update xph_goods,xph_brand set xph_goods.brand_id=xph_brand.id where xph_goods.sellernick=xph_brand.name" | mysql -hlocalhost -uroot -putaoinfo@2013  xiaopihai

echo "set names utf8; update xph_goods set name=replace(name, replace(sellernick, '旗舰店', ''), '') " | mysql -hlocalhost -uroot -putaoinfo@2013  xiaopihai
echo "set names utf8; update xph_goods set name=replace(name, replace(sellernick, '官方旗舰店', ''), '') " | mysql -hlocalhost -uroot -putaoinfo@2013  xiaopihai
echo "set names utf8; update xph_goods set name=replace(name, replace(sellernick, '童装旗舰店', ''), '') " | mysql -hlocalhost -uroot -putaoinfo@2013  xiaopihai
echo "set names utf8; update xph_goods set name=replace(name, '童装 ', '') " | mysql -hlocalhost -uroot -putaoinfo@2013  xiaopihai
echo "set names utf8; update xph_goods set name=replace(name, '孕妇装 ', '') " | mysql -hlocalhost -uroot -putaoinfo@2013  xiaopihai
echo "set names utf8; update xph_goods set name=replace(name, '孕妇 ', '') " | mysql -hlocalhost -uroot -putaoinfo@2013  xiaopihai
echo "set names utf8; update xph_goods set name=replace(name, '时尚 ', '') " | mysql -hlocalhost -uroot -putaoinfo@2013  xiaopihai
echo "set names utf8; update xph_goods set name=replace(name, '包邮', '') " | mysql -hlocalhost -uroot -putaoinfo@2013  xiaopihai
echo "set names utf8; update xph_goods set name=replace(name, '淘金币 ', '') " | mysql -hlocalhost -uroot -putaoinfo@2013  xiaopihai
echo "set names utf8; update xph_goods set name=replace(name, '2013 ', '') " | mysql -hlocalhost -uroot -putaoinfo@2013  xiaopihai
echo "set names utf8; update xph_goods set name=replace(name, '正品 ', '') " | mysql -hlocalhost -uroot -putaoinfo@2013  xiaopihai

echo "set names utf8; update xph_brand set name=replace(name, '官方旗舰店', '') " | mysql -hlocalhost -uroot -putaoinfo@2013  xiaopihai
echo "set names utf8; update xph_brand set name=replace(name, '母婴旗舰店', '') " | mysql -hlocalhost -uroot -putaoinfo@2013  xiaopihai
echo "set names utf8; update xph_brand set name=replace(name, '童鞋旗舰店', '') " | mysql -hlocalhost -uroot -putaoinfo@2013  xiaopihai
echo "set names utf8; update xph_brand set name=replace(name, '旗舰店', '') " | mysql -hlocalhost -uroot -putaoinfo@2013  xiaopihai
echo "set names utf8; update xph_goods set sellernick=replace(sellernick, '官方旗舰店', '') " | mysql -hlocalhost -uroot -putaoinfo@2013  xiaopihai
echo "set names utf8; update xph_goods set sellernick=replace(sellernick, '童鞋旗舰店', '') " | mysql -hlocalhost -uroot -putaoinfo@2013  xiaopihai
echo "set names utf8; update xph_goods set sellernick=replace(sellernick, '母婴旗舰店', '') " | mysql -hlocalhost -uroot -putaoinfo@2013  xiaopihai
echo "set names utf8; update xph_goods set sellernick=replace(sellernick, '旗舰店', '') " | mysql -hlocalhost -uroot -putaoinfo@2013  xiaopihai

echo "update xph_goods set sort =round(market_price/sell_price*100000/(volume*commission/pow(timestampdiff(hour, create_time,now())+2,1.5)))" |  mysql -hlocalhost -uroot -putaoinfo@2013  xiaopihai

echo "update xph_goods set discount=10*sell_price/market_price" |  mysql -hlocalhost -uroot -putaoinfo@2013  xiaopihai

/www/wdlinux/php/bin/php  brand_sort.php
/www/wdlinux/php/bin/php  cat_sort.php
/www/wdlinux/php/bin/php  getitemcats.php
