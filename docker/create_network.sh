#/bin/bash
# 创建执行环境内部网络
name=food_network
res=$(docker network ls|grep $name)
if [ -n "$res"  ];then
    echo "network $name exists"
else
    docker network create $name
fi
