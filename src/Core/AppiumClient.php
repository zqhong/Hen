<?php


namespace Hen\Core;


use PHPUnit_Extensions_Selenium2TestCase_Element;

class AppiumClient extends \PHPUnit_Extensions_AppiumTestCase
{
    /**
     * @var int
     */
    protected $findEleRetryCount = 5;

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
        if ($element instanceof  PHPUnit_Extensions_Selenium2TestCase_Element) {
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
            // TODO: log
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
            // TODO: log
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