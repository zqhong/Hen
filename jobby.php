<?php
use Hen\App;
use Jobby\Jobby;

require_once "bootstrap/autoload.php";

$jobby = new Jobby();

foreach (App::get()->config->get('accounts') as $appName => $accounts) {
    foreach ($accounts as $account) {
        $username = $account['username'];
        $password = $account['password'];
        $schedule = $account['schedule'];

        if (empty($username) || empty($password)) {
            continue;
        }

        $jobby->add(sprintf('%s-%s', $appName, $username), [
            'closure' => function () use ($appName, $username, $password, $schedule) {
                App::get()->platform($appName)
                    ->setUsername($username)
                    ->setPassword($password)
                    ->sign();
            },
            'schedule' => $schedule,
            'enabled' => true,
            'output' => sprintf('%s/data/logs/%s.log', rtrim(PATH_ROOT), $appName),
        ]);

    }
}

$jobby->run();