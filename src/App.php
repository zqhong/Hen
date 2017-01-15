<?php


namespace Hen;

use Hen\App\Adapter;
use Hen\Event\BootstrapEvent;
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
 */
class App extends Container
{
    private static $instance;

    public function __construct()
    {
        parent::__construct();

        $this['dispatcher'] = function () {
            return new EventDispatcher();
        };

        $this['logger'] = function () {
            return new Logger('hen');
        };

        $this['config'] = function () {
            $configDir = implode(DIRECTORY_SEPARATOR, [PATH_ROOT, 'config']);
            $configFiles = glob($configDir . DIRECTORY_SEPARATOR . '*.yml');

            $configFiles = array_filter($configFiles, function ($fileName) {
                if (stripos($fileName, 'example.yml') === false) {
                    return true;
                }
            });

            return new Config($configFiles);
        };

        $this->dispatcher->dispatch(BootstrapEvent::NAME, new BootstrapEvent());
        self::$instance = $this;
    }

    public static function get()
    {
        return self::$instance;
    }

    /**
     * @param string $appName
     * @return Adapter
     */
    public function platform($appName)
    {
        $objName = sprintf('Hen\\App\\%s', $appName);
        return new $objName();
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
