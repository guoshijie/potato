#/bin/bash
base=$(cd `dirname $0`; pwd)
/bin/bash  "$base/../create_network.sh"
base=$base"/../.."
docker run -d --name food-php -it --net food_network -v $base:/var/www/html registry.aliyuncs.com/shinc/php:5.6-fpm
