<?php


namespace Hen\App;


use Hen\Core\AppSignAdapter;

class JDRead extends AppSignAdapter
{
    protected function doLogin()
    {
        for ($i = 0; $i < 4; $i++) {
            $this->appiumClient->doSafeAction('name', '取消', 'click');
            $this->appiumClient->doSafeAction('name', '以后再说', 'click');
            $this->appiumClient->rightSwipe();
        }

        $this->appiumClient->doSafeAction('name', '我', 'click');
        $this->appiumClient->doSafeAction('id', 'com.jingdong.app.reader:id/account', 'setText', array($this->username));
        $this->appiumClient->doSafeAction('id', 'com.jingdong.app.reader:id/password', 'setText', array($this->password));
        $this->appiumClient->doSafeAction('id', 'com.jingdong.app.reader:id/login', 'click');

        // 等待 app 登录
        sleep(5);
    }

    protected function isLogin()
    {
        $this->appiumClient->doSafeAction('name', '我', 'click');
        
        $accountItemText = $this->appiumClient->byId('com.jingdong.app.reader:id/item_text');
        if (!empty($accountItemText)) {
            return true;
        }

        return false;
    }

    protected function doSign()
    {
        $this->appiumClient->doSafeAction('name', '书城', 'click');
        $this->appiumClient->doSafeAction('name', '我', 'click');
        $this->appiumClient->doSafeAction('name', '积分', 'click');
        $this->appiumClient->doSafeAction('name', '签到领积分', 'click');
        $this->appiumClient->doSafeAction('name', '确认', 'click');
    }

    protected function isSign()
    {
        $signButton = $this->appiumClient->byId('com.jingdong.app.reader:id/sign_to_get_scrore_tv');

        if (!empty($signButton) && mb_strpos($signButton->text(), '已连续签到') !== false) {
            return true;
        }

        return false;
    }

    protected function getAppPath()
    {
        return implode(DIRECTORY_SEPARATOR, [PATH_ROOT, 'data', 'apk', 'JD_Read.apk']);
    }

    protected function getAppActivity()
    {
        return '.activity.MainActivity';
    }

    protected function getAppPackage()
    {
        return 'com.jingdong.app.reader';
    }
}
