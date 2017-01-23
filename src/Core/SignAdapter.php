<?php


namespace Hen\Core;


use Hen\App;
use Hen\Event\AfterSignEvent;
use Hen\Event\BeforeSignEvent;
use Hen\Exception\LoginFailedException;
use Hen\Exception\SignFailedException;
use Monolog\Logger;
use Noodlehaus\Config;

abstract class SignAdapter
{
    /**
     * @var Config
     */
    protected $config;


    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var Logger
     */
    protected $logger;

    public function __construct()
    {
        $this->logger->debug('SignAdapter construct');
        $this->config = App::get()->config;
        $this->init();

    }

    protected function init()
    {
        $this->logger->debug('SignAdapter init');
    }

    /**
     * @param string $username
     * @return $this
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @param string $password
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    public function sign()
    {
        App::get()->dispatcher->dispatch(BeforeSignEvent::NAME, new BeforeSignEvent());
        $this->logger->info('SignAdapter doLogin');
        $this->doLogin();
        if ($this->isLogin() === false) {
            $this->logger->error('SignAdapter loginFailed', $this);
            throw new LoginFailedException();
        }

        $this->logger->info('SignAdapter doSign');
        $this->doSign();
        if ($this->isSign() === false) {
            $this->logger->error('SIgnAdapter signFailed', $this);
            throw new SignFailedException();
        }

        App::get()->dispatcher->dispatch(AfterSignEvent::NAME, new AfterSignEvent());
        return true;
    }

    abstract protected function doLogin();

    abstract protected function isLogin();

    abstract protected function doSign();

    abstract protected function isSign();
}
