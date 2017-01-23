FROM ubuntu:16.04
MAINTAINER zqhong <zqh0513@gmail.com>

#======================
# 设置 apt-get 软件源
#======================
COPY docker/etc/source.list /etc/apt/source.list

#============
# 安装 JDK
#============
RUN apt-get update -qqy \
  && apt-get -qqy --no-install-recommends install \
    ca-certificates \
    openjdk-8-jdk-headless \
    wget \
  && rm -rf /var/lib/apt/lists/* \
  && sed -i 's/securerandom\.source=file:\/dev\/random/securerandom\.source=file:\/dev\/urandom/' ./usr/lib/jvm/java-8-openjdk-amd64/jre/lib/security/java.security

#=================
# 安装 Android SDK
#=================
ENV ANDROID_SDK_VERSION 24.4.1
ENV ANDROID_HOME /opt/android-sdk-linux
ENV PATH ${PATH}:${ANDROID_HOME}/tools:${ANDROID_HOME}/platform-tools
RUN cd /opt \
  && wget --no-verbose http://dl.google.com/android/android-sdk_r$ANDROID_SDK_VERSION-linux.tgz -O android-sdk.tgz \
  && tar xzf android-sdk.tgz \
  && rm -f android-sdk.tgz \
  && cd android-sdk-linux/tools \
  && mv -f emulator64-arm emulator \
  && rm -f emulator64* emulator-* \
  && chmod +x android emulator

#=====================
# 配置 Android SDK Manager
#=====================
ENV ANDROID_COMPONENTS platform-tools,build-tools-24.0.0
RUN echo y | android update sdk --all --force --no-ui --filter ${ANDROID_COMPONENTS}

#===================
# 安装 Nodejs 和 Appium
#===================
ENV APPIUM_VERSION 1.5.3
RUN apt-get update -qqy \
  && apt-get -qqy --no-install-recommends install \
    nodejs \
    npm \
  && ln -s /usr/bin/nodejs /usr/bin/node \
  && npm install -g appium@$APPIUM_VERSION \
  && npm cache clean \
  && apt-get remove --purge -y npm \
  && apt-get autoremove --purge -y \
  && rm -rf /var/lib/apt/lists/*

#=========================
# 配置 USB 的 udev 规则
#=========================
ENV UDEV_REMOTE_FILE https://raw.githubusercontent.com/M0Rf30/android-udev-rules/master/ubuntu/51-android.rules
RUN mkdir /etc/udev/rules.d \
  && wget --no-verbose $UDEV_REMOTE_FILE -O /etc/udev/rules.d/51-android.rules


#===========================
# Android SDK Manager (API)
#===========================
ENV AVD_VERSION 19
ENV ANDROID_COMPONENTS android-$AVD_VERSION,sys-img-armeabi-v7a-android-$AVD_VERSION
RUN echo y | android update sdk --all --force --no-ui --filter ${ANDROID_COMPONENTS}

#=========================
# 创建安卓模拟器
#=========================
RUN android create avd --force --name android-$AVD_VERSION --target android-$AVD_VERSION \
  --device "Nexus S" --abi armeabi-v7a --skin WVGA800

#================================
# 安装依赖软件 - php、git、vim
#===============================
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

# 开放 Appium Server 的端口
EXPOSE 4723

CMD ["/zqhong/code/hen/docker/bin/run.sh"]
