## Mac OS,Windows docker环境搭建
1. 安装Docker-Toolbox
2. 创建docker运行虚拟环境
    > docker-machine create --driver virtualbox default
      docker-machine ls 查询已创建的虚拟机,name为default
      eval "$(docker-machine env default)"

## 下载镜像
1. 配置加速器
    > docker-machine ssh default "echo 'EXTRA_ARGS=\"--registry-mirror=https://n8tyuuz0.mirror.aliyuncs.com\"' | sudo tee -a /var/lib/boot2docker/profile"
      docker-machine restart default
2. 登录至阿里云镜像服务
    > docker login --username=admin@shinc.net registry.aliyuncs.com
    密码:shihe123456
    > docker pull registry.aliyuncs.com/shinc/php:5.6-fpm
    > docker pull registry.aliyuncs.com/shinc/food-nginx

##运行开发环境
    ```
    - cd $project/docker
    - sh create_network.sh
    - sh nginx/run.sh
    - sh php-fpm/run.sh
    ```
