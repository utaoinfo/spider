#!/bin/bash -
#
# 抓取并分析数据. 包括商品id, 现价, 原价, 月销售量, 评论书, 以及分类等.
#
CURRENT_LOCATION=$(networksetup -getcurrentlocation)
[[ "$CURRENT_LOCATION" == "work" ]] && export http_proxy="http://web-proxy.oa.com:8080" || export http_proxy=""

test $# -eq 0 && urls="www.jd.com" ||  urls="$*"
ulimit -u 1024
dt=$(date +%Y%m%d)
curl_flag=" -L -A Mozilla/5.0 -f -s  -m 10 -H Accept-Encoding:gzip,deflate "
for url in $urls ;do
	echo $url
	curl $curl_flag $url | gunzip | iconv -f cp936 -t utf-8  | grep  -A1 'productShop-name' >> tmp.html
	dos2unix tmp.html
done

cat tmp.html | perl -pe 's/.*href=.([^"]+).*/$1&sort=d/g'  | grep -v '^-' | awk 'NR%2==0{print $0}NR%2==1{printf "%s ", $0}' |\
	 sort -u  |grep '旗舰店' >qijiandian.log
#rm -rf tmp.html
