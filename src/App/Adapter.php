<?php


namespace Hen\App;


use Goutte\Client as GoutteClient;
use Hen\App;
use Hen\Event\AfterSignEvent;
use Hen\Event\BeforeSignEvent;
use Hen\Exception\LoginFailedException;
use Hen\Exception\SignFailedException;
use Noodlehaus\Config;

abstract class Adapter
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var GoutteClient
     */
    protected $httpClient;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

    public function __construct()
    {
        $this->config = App::get()->config;
        $this->httpClient = new GoutteClient();
        $this->setDefaultHeaders();
    }

    protected function setDefaultHeaders()
    {
        $this->httpClient->setHeader('User-Agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36');
        $this->httpClient->setHeader('Accept-Language', 'zh-CN,zh;q=0.8,en;q=0.6');
        $this->httpClient->setHeader('Accept-Encoding', 'gzip');
        $this->httpClient->setHeader('Accept', 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8');
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
        $this->doLogin();
        if ($this->isLogin() === false) {
            throw new LoginFailedException();
        }

        $this->doSign();
        if ($this->isSign() === false) {
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
