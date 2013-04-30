#!/bin/bash -x

echo "update xph_goods,spider set xph_goods.market_price=spider.old_price  where xph_goods.goods_no=spider.product_id"  | mysql -hlocalhost -uroot -putaoinfo@2013 xiaopihai

echo "update xph_goods,spider set xph_goods.small_img=pic, xph_goods.list_img=replace(pic,'_160x160','_80x80') where xph_goods.goods_no=spider.product_id " | mysql -hlocalhost -uroot -putaoinfo@2013  xiaopihai
