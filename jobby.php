<?php
use Hen\App;
use Jobby\Jobby;

require_once "bootstrap/autoload.php";

$jobby = new Jobby();
$jobWorkerPath = implode(DIRECTORY_SEPARATOR, [PATH_ROOT, 'bin', 'job-worker.php']);

foreach (App::get()->config->get('accounts') as $appName => $accounts) {
    foreach ($accounts as $account) {
        $username = $account['username'];
        $password = $account['password'];
        $schedule = $account['schedule'];

        if (empty($username) || empty($password)) {
            continue;
        }

        $jobby->add(sprintf('%s-%s', $appName, $username), [
            'command' => sprintf('php %s %s %s %s', $jobWorkerPath, $appName, $username, $password),
            'schedule' => $schedule,
            'enabled' => true,
            'output' => sprintf('%s/data/logs/%s.log', rtrim(PATH_ROOT), $appName),
        ]);

    }
}

$jobby->run();