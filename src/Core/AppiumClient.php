<?php


namespace Hen\Core;

use Hen\App;
use Monolog\Logger;
use PHPUnit_Extensions_Selenium2TestCase_Element;

class AppiumClient extends \PHPUnit_Extensions_AppiumTestCase
{
    /**
     * @var int
     */
    protected $findEleRetryCount = 5;

    /**
     * @var Logger
     */
    protected $logger;

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->logger = App::get()->logger;
    }

    /**
     * @param $strategy
     * @param $value
     * @param $action
     * @param array $params
     * @return bool
     */
    public function doSafeAction($strategy, $value, $action, array $params = array())
    {
        $element = $this->by($strategy, $value);
        $this->logger->debug('AppiumClient - doSafeAction', [
            'strategy' => $strategy,
            'value' => $value,
            'action' => $action,
            'params' => $params,
            'element' => $element,
        ]);

        if ($element instanceof PHPUnit_Extensions_Selenium2TestCase_Element) {
            return call_user_func_array([$element, $action], $params);
        }

        return false;
    }

    /**
     * @param $strategy
     * @param $value
     * @return bool|PHPUnit_Extensions_Selenium2TestCase_Element|static
     */
    public function by($strategy, $value)
    {
        for ($i = 0; $i < $this->findEleRetryCount; $i++) {
            $element = $this->safeBy($strategy, $value);
            if ($element instanceof PHPUnit_Extensions_Selenium2TestCase_Element) {
                return $element;
            }
            $this->logger->debug(sprintf('AppiumClient - by method - try count: %d', $i + 1));
            sleep(1);
        }

        return false;
    }

    /**
     * @param $strategy
     * @param $value
     * @return bool|PHPUnit_Extensions_Selenium2TestCase_Element|static
     */
    public function safeBy($strategy, $value)
    {
        try {
            return parent::by($strategy, $value);
        } catch (\Exception $e) {
            $this->logger->error('AppiumClient safeBy error', ['exception' => $e]);
            return false;
        }
    }

    /**
     * @return $this|bool
     */
    public function leftSwipe()
    {
        return $this->swipe(0, 0, 10, 0);
    }

    /**
     * @param $startX
     * @param $startY
     * @param $endX
     * @param $endY
     * @param int $duration
     * @return $this|bool
     */
    public function swipe($startX, $startY, $endX, $endY, $duration = 800)
    {
        try {
            return parent::swipe($startX, $startY, $endX, $endY, $duration);
        } catch (\Exception $e) {
            $this->logger->error('AppiumClient swipe error', ['excpetion' => $e]);
            return false;
        }
    }

    /**
     * @return bool|AppiumClient
     */
    public function rightSwipe()
    {
        return $this->swipe(10, 0, 0, 0);
    }
}
