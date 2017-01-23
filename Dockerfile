FROM rgonalo/appium-emulator
MAINTAINER zqhong <zqh0513@gmail.com>


#======================
# 设置 apt-get 软件源
#======================
COPY docker/etc/source.list /etc/apt/source.list

#==============
# 安装依赖软件
#==============
RUN apt-get update -qqy \
  && apt-get -qqy --no-install-recommends install \
    cron \
    git \
    php \
    php-curl \
    php-mbstring \
    php-xml \
    vim

#===============================
# 将项目代码复制到 docker 容器内
#===============================
ENV HEN_INSTALL_DIR /zqhong/code/hen
RUN mkdir -p $HEN_INSTALL_DIR
COPY . $HEN_INSTALL_DIR/

#=====================
# 安装和配置 Composer
#=====================
ENV COMPOSER_ALLOW_SUPERUSER 1
RUN chmod +x $HEN_INSTALL_DIR/docker/bin/composer-setup.php \
    && php $HEN_INSTALL_DIR/docker/bin/composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && composer config -g repo.packagist composer https://packagist.phpcomposer.com \
    && composer self-update

#==============
# 正式部署 Hen
#==============
WORKDIR $HEN_INSTALL_DIR
RUN composer install --no-dev --prefer-source
RUN mkdir -p /var/spool/cron/crontabs \
    && echo "* * * * * cd $HEN_INSTALL_DIR/ && php jobby.php" > /var/spool/cron/crontabs/root \
    && chmod 600 /var/spool/cron/crontabs/root

RUN chmod +x $HEN_INSTALL_DIR/docker/bin/run.sh

CMD ["/zqhong/code/hen/docker/bin/run.sh"]
