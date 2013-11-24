<?php
// this script will check Alexa 1 million websites for HTTPS availability and if a HSTS header is set.
// HSTS = HTTP Strict Transport Security
// http://en.wikipedia.org/wiki/HTTP_Strict_Transport_Security

require 'PHPGangsta/HSTSChecker.php';
require 'PHPGangsta/AsyncRunner.php';
require 'dbconnect.php';

$firstRunWithEmptyDatabase = false;  // then we can skip the "has already been processed" check
$max = 400;
$threads = array();

class WebWorker extends Worker {
    public function run(){}
}

/* start some workers */
$thread = 0;
while (@$thread++<$max) {
    $threads[$thread] = new WebWorker();
    $threads[$thread]->start();
}

$bigList = array();
$list = array();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM HstsResult WHERE Hostname = ?");

$rank = 1;
$alexa = file('public/download/top-1m.csv', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

$chunks = array_chunk($alexa, 5000);
foreach ($chunks as $chunk) {
    foreach ($chunk as $hostname) {
        if (!$firstRunWithEmptyDatabase) {
            // has this hostname already been processed?
            $stmt->execute(array($hostname));
            $count = $stmt->fetchColumn();

            if ($count >= 1) {
                $rank++;
                echo '-';
                continue;
            }
        }
        echo "c";

        $list[] = new \PHPGangsta\AsyncRunner($hostname, $rank);

        $rank++;
    }

    foreach ($list as $l) {
        $threads[array_rand($threads)]->stack($l);
        usleep(10000);
    }

    echo "\npitstop, ".count($chunk)." jobs have been started, waiting for all to finish, then start all over\n";
    /* wait for completion */
    foreach ($threads as $oneThread) {
        $oneThread->shutdown();
    }
    echo "\nall workers stopped, putting results into DB\n";

    $insertStmt = $pdo->prepare("INSERT INTO HstsResult (AlexaRank, Hostname, Https, HasHeader, MaxAge, IncludeSubdomains) VALUES (?, ?, ?, ?, ?, ?)");
    $pdo->beginTransaction();
    foreach ($list as $l) {
        $insertStmt->execute($l->getData());
    }
    $pdo->commit();

    echo "restarting workers\n";
    $thread = 0;
    $threads = array();
    $bigList = array_merge($bigList, $list);
    $list = array();
    while (@$thread++<$max) {
        $threads[$thread] = new WebWorker();
        $threads[$thread]->start();
    }
}

/* wait for completion */
foreach ($threads as $thread) {
    $thread->shutdown();
}
