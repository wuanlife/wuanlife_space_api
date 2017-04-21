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
    
#### 3.安装epel源
    yum -y install epel-release
#### 4.更新系统
    yum -y update
#### 5.安装git、vim、unzip等必要组件
    yum -y install git vim unzip wget
### 二、安装PHP环境
#### 1.安装MariaDB
    yum -y install mariadb mariadb-server net-tools
#### 2.安装PHP
    yum -y install php-fpm php-cli php-mysql php-gd php-ldap php-odbc php-pdo php-pecl-memcache php-pear php-mbstring php-xml php-xmlrpc php-mbstring php-snmp php-soap php-devel
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
    git clone https://github.com/wuanlife/wuanlife_api.git
    wget https://files.phpmyadmin.net/phpMyAdmin/4.4.15.10/phpMyAdmin-4.4.15.10-all-languages.zip
    unzip phpMyAdmin-4.4.15.10-all-languages.zip
    mv phpMyAdmin-4.4.15.10-all-languages phpmyadmin
    chown -R root:nginx /home/www
    chmod 775 /home/www
#### 8.修改nginx配置
    cd /etc/nginx
    wget https://raw.githubusercontent.com/wuanlife/wuanlife_api/wiki/config/nginx.conf -O nginx.conf
    cd conf.d
    wget https://raw.githubusercontent.com/wuanlife/wuanlife_api/wiki/config/default.conf
    wget https://raw.githubusercontent.com/wuanlife/wuanlife_api/wiki/config/phpmyadmin.conf
    systemctl reload nginx
访问下http://YourIP:8080和http://YourIP:800，应该已经部署好了
### 三、部署前端代码
#### 1.安装node.js环境
    yum -y install nodejs npm
#### 2.下载前端代码
    cd /home/www/html/
    git clone https://github.com/wuanlife/wuanlife.git

#### 3.配置MongoDB
追加yum源

    vim /etc/yum.repos.d/mongodb.repo

追加如下内容后保存

    [mongodb-org-2.6]
    name=MongoDB 2.6 Repository
    baseurl=http://downloads-distro.mongodb.org/repo/redhat/os/x86_64/
    gpgcheck=0
    enabled=1

安装mongodb

    yum -y install mongodb-org

配置mongo

    systemctl start mongod
    /sbin/chkconfig mongod on

指定mongo数据文件位置
    
    cd /home/www/html/wuanlife
    mkdir db
    mongod --dbpath db
    
#### 4.配置node.js
    npm install
    set DEBUG=myapp & npm start
ctrl+c退出

    npm install forever -g
    export PORT=80
    PORT=80 node app.js

    forever start --uid wuanlife bin/www

访问下http://YourIP，应该可以访问了。
    
### 四、修改配置
其中，API代码需修改数据库配置，前端代码需修改接口地址，修改后重启node。

    vim /home/www/html/wuanlife_api/Config/dbs.php
    vim /home/www/html/wuanlife/config/config.js
    forever restart --uid wuanlife bin/www
