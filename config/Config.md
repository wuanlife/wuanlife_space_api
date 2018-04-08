# 午安网代码部署
本文档仅适用于全新安装，并只适用于CentOS7系统。
### 一、准备工作
#### 1.关闭SELinux

    vim /etc/selinux/config

如果SELINUX=enforcing，将其改为disabled，然后重启服务器

    SELINUX=disabled

#### 2.关闭防火墙
    
    systemctl stop firewalld.service
    systemctl disable firewalld.service
    
#### 3.更新yum源
    rpm -Uvh https://mirror.webtatic.com/yum/el7/epel-release.rpm
    rpm -Uvh https://mirror.webtatic.com/yum/el7/webtatic-release.rpm
#### 4.更新系统
    yum -y update
#### 5.安装git、vim、unzip等必要组件
    yum -y install git vim unzip wget
### 二、安装PHP环境
#### 1.安装MariaDB
    yum -y install mariadb mariadb-server net-tools
#### 2.安装PHP7
    yum -y install php72w php72w-fpm php72w-cli php72w-common php72w-gd php72w-ldap php72w-mbstring php72w-mcrypt php72w-mysqlnd php72w-pdo
#### 3.安装nginx
    yum -y install nginx
#### 4.启动环境并设置自启
    systemctl enable mariadb.service
    systemctl start mariadb.service
    systemctl enable php-fpm.service
    systemctl start php-fpm.service
    systemctl enable nginx.service
    systemctl start nginx.service
#### 5.设置MariaDB密码
    mysql_secure_installation
#### 6.设置session
    mkdir /var/lib/php/session
    chmod 667 /var/lib/php/session
    chown -R root:nginx /var/lib/php/session
编辑php.ini

    vim /etc/php.ini
查找session.save_path，修改为

    session.save_path ="/var/lib/php/session"
#### 7.下载API代码和phpMyAdmin
    mkdir /home/www
    cp /usr/share/nginx/html /home/www/html -rf
    cd /home/www/html
    git clone https://github.com/wuanlife/wuanlife_space_api.git
    wget https://files.phpmyadmin.net/phpMyAdmin/4.8.0/phpMyAdmin-4.8.0-all-languages.zip
    unzip phpMyAdmin-4.8.0-all-languages.zip
    mv phpMyAdmin-4.8.0-all-languages phpmyadmin
    chown -R root:nginx /home/www
    chmod 775 /home/www
#### 8.修改nginx配置
    cd /etc/nginx
    wget https://raw.githubusercontent.com/wuanlife/wuanlife_api/wiki/config/nginx.conf -O nginx.conf
    cd conf.d
    wget https://raw.githubusercontent.com/wuanlife/wuanlife_api/wiki/config/default.conf
    wget https://raw.githubusercontent.com/wuanlife/wuanlife_api/wiki/config/phpmyadmin.conf
    systemctl reload nginx
#### 9.修改配置
    vim /home/www/html/wuanlife_space_api/application/config/config.php
    vim /home/www/html/wuanlife_space_api/application/config/database.php
### 三、部署前端代码
#### 1.安装node.js环境
    curl --silent --location https://rpm.nodesource.com/setup_8.x | sudo bash -
    yum -y install nodejs
#### 2.下载前端代码
    cd /home/www/html/
    git clone https://github.com/wuanlife/wuanlife_space.git
#### 3.配置项目
    vim /home/www/html/wuanlife_space/config/prod.env.js
#### 4.编译项目
    cd wuanlife_space
    npm install
    npm run build:prod
    
#### 4.配置nginx
    cd /etc/nginx/conf.d
    wget https://raw.githubusercontent.com/wuanlife/wuanlife_api/wiki/config/wuanlife.conf
    systemctl reload nginx
