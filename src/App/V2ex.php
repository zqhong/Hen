<?php


namespace Hen\App;

use Hen\Core\WebSignAdapter;
use Hen\Exception\MatchNothingException;

class V2ex extends WebSignAdapter
{
    const HOME_URL = 'https://www.v2ex.com/';
    const LOGIN_URL = 'https://www.v2ex.com/signin';
    const SIGN_URL = 'https://www.v2ex.com/mission/daily';

    protected function doLogin()
    {
        $crawler = $this->httpClient->request('GET', self::LOGIN_URL);
        $form = $crawler->selectButton('登录')->form();

        $usernameFieldName = $crawler->filter('#Main input[class="sl"][type="text"]')->first()->attr('name');
        $passwordFieldName = $crawler->filter('#Main input[class="sl"][type="password"]')->first()->attr('name');

        $form[$usernameFieldName] = $this->username;
        $form[$passwordFieldName] = $this->password;

        $this->logger->debug('V2EX - DoLogin', ['form' => $form]);
        $this->httpClient->submit($form);
    }


    protected function isLogin()
    {
        $crawler = $this->httpClient->request('GET', self::HOME_URL);

        // 页面需要同时存在当前登录用户名和登出这两个字符串，才认为登录成功。
        $pageContent = $crawler->html();
        $this->logger->debug('V2EX - IsLogin', ['pageContent' => $pageContent]);
        if (strpos($pageContent, $this->username) !== false && strpos($pageContent, '登出') !== false) {
            $this->logger->debug('V2EX - IsLogin - true');
            return true;
        }

        $this->logger->debug('V2EX - IsLogin - false. Check pageContent variable');
        return false;
    }

    protected function doSign()
    {
        $crawler = $this->httpClient->request('GET', self::SIGN_URL);

        // <input type="button" class="super normal button" value="领取 X 铜币" onclick="location.href = '/mission/daily/redeem?once=98209';">
        $onClickValue = $crawler->filter('input[value="领取 X 铜币"]')->first()->attr('onclick');
        $this->logger->debug('V2EX - DoSign', ['onClickValue' => $onClickValue]);

        $matches = [];
        if (preg_match('@(/mission/daily/redeem\?once=\d+)@', $onClickValue, $matches) === 0) {
            $this->logger->debug('V2EX - DoSign - false. Match nothing, check onClickValue.');
            throw new MatchNothingException();
        }
        $uri = trim(self::HOME_URL, '/') . $matches[0];
        $this->logger->debug('V2EX - DoSign - true', ['uri' => $uri]);

        $this->httpClient->request('GET', $uri);
    }

    protected function isSign()
    {
        $crawler = $this->httpClient->request('GET', self::SIGN_URL);

        $pageContent = $crawler->html();
        $this->logger->debug('V2EX - IsSign', ['pageContent' => $pageContent]);
        if (strpos($pageContent, '每日登录奖励已领取') === false) {
            $this->logger->debug('V2EX - IsSign - false');
            return false;
        }

        $this->logger->debug('V2EX - IsSign - true');
        return true;
    }
}
