#!/bin/bash -x

for file in $(ls *.php *.sh);do
	sendEmail -f 252653283@qq.com -t khb.hnu@gmail.com  -u "$file" -m "$(cat $file)"
done
