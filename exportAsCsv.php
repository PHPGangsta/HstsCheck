<?php

require 'dbconnect.php';

$fp = fopen('results-'.date('Y-m-d').'.csv', 'w');
fputcsv($fp, array('HstsResultId', 'Hostname', 'Https', 'HasHeader', 'MaxAge', 'IncludeSubdomains'));

$sql = 'SELECT HstsResultId, Hostname, Https, HasHeader, MaxAge, IncludeSubdomains FROM HstsResult  ORDER BY HstsResultId ASC';
foreach ($pdo->query($sql, PDO::FETCH_ASSOC) as $row) {
    fputcsv($fp, $row);
}
fclose($fp);
