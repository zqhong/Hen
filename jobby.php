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
            App::get()->logger->warning('jobby - add job, username or password empty', ['account' => $account]);
            continue;
        }

        $jobName = sprintf('%s-%s', $appName, $username);
        $params = [
            'command' => sprintf('php %s %s %s %s', $jobWorkerPath, $appName, $username, $password),
            'schedule' => $schedule,
            'enabled' => true,
            'output' => sprintf('%s/data/logs/%s.jobby.log', rtrim(PATH_ROOT), $appName),
        ];
        App::get()->logger->debug(sprintf('Add a jobby job, name: %s', $jobName), ['params' => $params]);
        $jobby->add($jobName, $params);
    }
}

$jobby->run();
