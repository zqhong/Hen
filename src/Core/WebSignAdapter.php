<?php


namespace Hen\Core;

use Goutte\Client as GoutteClient;
use Hen\App;

abstract class WebSignAdapter extends SignAdapter
{
    /**
     * @var GoutteClient
     */
    protected $httpClient;

    public function init()
    {
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
}
