# Hen
Hen 是一个由 PHP 编写的签到工具，目前仅适配了 V2EX 和京东阅读。

# 安装
从 GitHub 中下载该项目
```
$ git clone https://github.com/zqhong/Hen.git
```

使用 Composer 安装 Hen 的依赖包
```
$ composer install --no-dev
```

将 `config/common.example.yml` 重命名为 `config/common.yml`
```
$ mv config/common.example.yml config/common/yml
```

将以下任务将如 crontab 中
```
* * * * * cd /path/to/project && php jobby.php 1>> /dev/null 2>&1
```

# 配置文件
示例配置内容
```
accounts:
  v2ex:
    - username: username1
      password: password2
      schedule: "1 8 * * *"
    - username: username1
      password: password2
      schedule: "1 8 * * *"
  jdread:
    - username: username1
      password: password1
      schedule: "1 0 * * *"
```

这个配置文件的意思：
* 有两个 V2EX 的帐号，需要在每天的 08:01 签到；
* 有一个京东阅读（app）的帐号，需要在每天的 00:01 签到。

schedule 的写法请参考：[Cron and Crontab usage and examples](https://www.pantz.org/software/cron/croninfo.html)

一个简单的例子：每月的12号的 02:00 运行 `/usr/bin/find` 命令
```
# Minute   Hour   Day of Month       Month          Day of Week        Command
# (0-59)  (0-23)     (1-31)    (1-12 or Jan-Dec)  (0-6 or Sun-Sat)
    0        2          12             *                *            /usr/bin/find
```

