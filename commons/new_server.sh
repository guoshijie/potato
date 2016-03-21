#/bin/bash
if [ $# -eq 0 ]       ##判断参数是否存在
then
exit

newServerName=$1
newServer=${newServerName}Server
cp -r ../DemoServer  ../$newServer
sed -i "s/demo/$newServer/g" ../$newServer/api/composer.json 
