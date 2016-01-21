[TOC]

#餐饮平台

## 服务及接口

|服务名称|host地址|占用端口|描述|
|-|-|-|-|
|AutoIDServer|auto.id.host|20100|自增ID生成器|
|UserServer|user.server.host|20200|用户服务|
|ShopServer|shop.server.host|20300|商品服务|
|web服务||||
|展销前段|...|8080|..|

## 新建一个服务

```shell
composer global require "laravel/lumen-installer=~1.0"
```

```shell
$
$ mkdir TestServer/api
$ cd TestServer
$ composer create-project --prefer-dist laravel/lumen server
$ cd ../api
$ composer init --name="shinc/shop-server-api" --type=project -n
$ 
```