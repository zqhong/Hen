<?php


namespace Hen\Core;

abstract class AppSignAdapter extends SignAdapter
{
    /**
     * @var AppiumClient
     */
    protected $appiumClient;

    protected function init()
    {
        $this->appiumClient = new AppiumClient();
        $this->appiumClient->setPort($this->config->get('appium.port'));
        $this->appiumClient->setDesiredCapabilities([
            'app' => $this->getAppPath(),
            'deviceName' => $this->config->get('appium.deviceName'),
            'platformName' => $this->config->get('appium.platformName'),
            'platformVersion' => $this->config->get('appium.platformVersion'),
            'unicodeKeyboard' => $this->config->get('appium.unicodeKeyboard'),
            'resetKeyboard' => $this->config->get('appium.resetKeyboard'),
        ]);

        $this->appiumClient->startActivity([
            'appPackage' => $this->getAppPackage(),
            'appActivity' => $this->getAppActivity(),
        ]);
    }

    abstract protected function getAppPath();

    abstract protected function getAppActivity();

    abstract protected function getAppPackage();
}
