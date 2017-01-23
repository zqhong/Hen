<?php


namespace Hen;

use Hen\Core\SignAdapter;
use Hen\Event\BootstrapEvent;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Noodlehaus\Config;
use Pimple\Container;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Class App
 *
 * @package Hen
 * @property EventDispatcher $dispatcher
 * @property Logger $logger
 * @property Config $config
 * @property string DATA_PATH
 * @property string CONFIG_PATH
 * @property string APP_PATH
 */
class App extends Container
{
    private static $instance;

    public function __construct()
    {
        parent::__construct();

        $this->setCorePath();

        $this['dispatcher'] = function () {
            return new EventDispatcher();
        };

        $this['config'] = function () {
            $configDir = implode(DIRECTORY_SEPARATOR, [PATH_ROOT, 'config']);
            $configFiles = glob($configDir . DIRECTORY_SEPARATOR . '*.yml');

            $configFiles = array_filter($configFiles, function ($fileName) {
                if (stripos($fileName, 'example.yml') === false) {
                    return true;
                }
                return false;
            });

            return new Config($configFiles);
        };

        $config = $this['config'];
        $this['logger'] = function () use ($config) {
            $logger = new Logger('hen');
            $logger->pushHandler(new StreamHandler(implode(DIRECTORY_SEPARATOR, [$this->DATA_PATH, 'logs', 'app.warning']), Logger::WARNING));

            if ($config->get('app.debug') === true) {
                $logger->pushHandler(new StreamHandler(implode(DIRECTORY_SEPARATOR, [$this->DATA_PATH, 'logs', 'app.debug']), Logger::DEBUG));
            }

            return $logger;
        };

        $this->logger->debug('app bootstrap...');
        $this->dispatcher->dispatch(BootstrapEvent::NAME, new BootstrapEvent());
        self::$instance = $this;
    }

    protected function setCorePath()
    {
        $this['DATA_PATH'] = function () {
            return PATH_ROOT . DIRECTORY_SEPARATOR . 'data';
        };
        $this['CONFIG_PATH'] = function () {
            return PATH_ROOT . DIRECTORY_SEPARATOR . 'config';
        };
        $this['APP_PATH'] = function () {
            return PATH_ROOT . DIRECTORY_SEPARATOR . 'src';
        };
    }

    public static function get()
    {
        return self::$instance;
    }

    /**
     * @param string $appName
     * @return SignAdapter
     */
    public function platform($appName)
    {
        $objName = sprintf('Hen\\App\\%s', $appName);
        $objInstance = new $objName();

        $this->logger->debug(sprintf('Platform: %s, objName: %s', $appName, $objName), $objInstance);

        return $objInstance;
    }

    public function __get($name)
    {
        if (isset($this[$name])) {
            return $this[$name];
        } else {
            throw new \InvalidArgumentException("Property \"$name\" does not exist.");
        }
    }
}
