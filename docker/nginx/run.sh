#/bin/bash
# 创建nginx容器
base=$(cd `dirname $0`;pwd)
/bin/bash  "$base/../create_network.sh"
docker run -d -it --name food-nginx --net food_network -p 8080:8080 -v $base/conf.d:/etc/nginx/conf.d registry.aliyuncs.com/shinc/food-nginx
