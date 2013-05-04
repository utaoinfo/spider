spider
======

spider


## step
+ nohup  ./data.sh $(cat qijiandian.log) &
+ nohup  cat dian.log  | while read name;do  /www/wdlinux/php/bin/php goods_brands_import.php $name; done  &
+ ./flush_data.sh
+ /www/wdlinux/php/bin/php getitem.php
+ /www/wdlinux/php/bin/php getitemcat.php
