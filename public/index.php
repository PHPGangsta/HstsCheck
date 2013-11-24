<?php
require '../dbconnect.php';




?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>HSTS-Check Website by PHPGangsta</title>
    <meta name="author" content="Michael Kliewe, Germany" />
    <meta name="robots" content="index, follow, noarchive" />
    <meta name="keywords" lang="de" content="HSTS, security, HTTPS, Header, HTTP Strict Transport Security" />
    <meta name="description" lang="de" content="This is a website with check results analyzing the usage of the HSTS header in the Alexa Top 1 Million websites" />
    <meta name="viewport" content="width=device-width, user-scalable=no" />

    <link href="/css/style.css" rel="stylesheet" type="text/css" media="all" />
</head>
<body>
    <h1>Alexa Top 1 Million Websites have been analyzed to find out usage patterns of HTTPS, <a href="http://en.wikipedia.org/wiki/HTTP_Strict_Transport_Security" target="_blank">HSTS</a> and its settings.</h1>
    <h2>Date of analysis: November 23, 2013</h2>
    <div class="tablebox">
        <h3>HTTPS usage in percent:</h3>
        <div>
            <canvas id="httpsUsageChart" width="370" height="370"></canvas>
            <div id="httpsUsageChartLegend"></div>
        </div>
        <?
        $sql = 'SELECT COUNT(*) AS co FROM HstsResult';
        $resultsTotal = $pdo->query($sql)->fetchColumn();

        $sql = 'SELECT COUNT(*) AS co FROM HstsResult WHERE Https=1';
        $httpsActive = $pdo->query($sql)->fetchColumn();
        ?>
        <script>
            var httpsUsageData = [
                {
                    value: <?=$httpsActive ?>,
                    color:"#F38630",
                    label : 'Using HTTPS',
                    labelColor : 'white',
                    labelFontSize : '16'
                },
                {
                    value : <?=($resultsTotal-$httpsActive) ?>,
                    color : "#E0E4CC",
                    label : 'HTTP only',
                    labelColor : 'black',
                    labelFontSize : '16'
                }
            ]
        </script>
    </div>

    <div class="tablebox">
        <h3>HSTS active on HTTPS websites:</h3>
        <div>
            <canvas id="hstsUsageChart" width="370" height="370"></canvas>
            <div id="hstsUsageChartLegend"></div>
        </div>

        <?
        $sql = 'SELECT COUNT(*) AS co FROM HstsResult WHERE Https=1 AND HasHeader=1 AND MaxAge!=0';
        $hstsActive = $pdo->query($sql)->fetchColumn();
        ?>
        <script>
            var hstsUsageData = [
                {
                    value: <?=$hstsActive ?>,
                    color:"#F38630",
                    label : 'Using HSTS',
                    labelColor : 'white',
                    labelFontSize : '16'
                },
                {
                    value : <?=($httpsActive-$hstsActive) ?>,
                    color : "#E0E4CC",
                    label : 'No HSTS',
                    labelColor : 'black',
                    labelFontSize : '16'
                }
            ]
        </script>
        <div style="margin-top: 10px;">
            Only <?=$hstsActive ?> websites of the Alexa Top 1 Million support HTTPS+HSTS.
        </div>
    </div>

    <div class="tablebox">
        <h3>HSTS max-age:</h3>
        <div>
            <canvas id="maxAgeUsageChart" width="370" height="370"></canvas>
            <div id="maxAgeUsageChartLegend"></div>
        </div>

        <?
        $sql = 'SELECT COUNT(*) AS co FROM HstsResult WHERE Https=1 AND HasHeader=1 AND MaxAge>0 AND MaxAge <= 2592000';
        $maxAge1 = $pdo->query($sql)->fetchColumn();
        $sql = 'SELECT COUNT(*) AS co FROM HstsResult WHERE Https=1 AND HasHeader=1 AND MaxAge > 2592000 AND MaxAge < 31536000';
        $maxAge2 = $pdo->query($sql)->fetchColumn();
        $sql = 'SELECT COUNT(*) AS co FROM HstsResult WHERE Https=1 AND HasHeader=1 AND MaxAge >= 31536000';
        $maxAge3 = $pdo->query($sql)->fetchColumn();
        ?>
        <script>
            var maxAgeUsageData = [
                {
                    value: <?=$maxAge1 ?>,
                    color:"#F38630",
                    label : 'less than 1 month',
                    labelColor : 'white',
                    labelFontSize : '16'
                },
                {
                    value : <?=$maxAge2 ?>,
                    color : "#E0E4CC",
                    label : '1 month to 1 year',
                    labelColor : 'black',
                    labelFontSize : '16'
                },
                {
                    value : <?=$maxAge3 ?>,
                    color : "#48FF7C",
                    label : 'more than 1 year',
                    labelColor : 'white',
                    labelFontSize : '16'
                }
            ]
        </script>
    </div>

    <div class="tablebox">
        <h3>Top 15 HTTPS Websites with HSTS enabled:</h3>
        <h4>(sorted by AlexaRank)</h4>
        <table>
            <tr>
                <td>AlexaRank</td>
                <td>Hostname</td>
                <td>MaxAge</td>
                <td>IncludeSubdomains</td>
            </tr>
            <?  $sql = 'SELECT AlexaRank, Hostname, MaxAge, IncludeSubdomains FROM HstsResult WHERE Https=1 AND HasHeader=1 AND MaxAge>0 ORDER BY AlexaRank ASC LIMIT 15';
            foreach ($pdo->query($sql) as $row) { ?>
                <tr>
                    <td class="number"><?=$row['AlexaRank'] ?></td>
                    <td><?=$row['Hostname'] ?></td>
                    <td class="number"><?=$row['MaxAge'] ?></td>
                    <td class="number"><?=$row['IncludeSubdomains'] ?></td>
                </tr>
            <?  } ?>
        </table>
    </div>

    <div class="tablebox">
        <h3>Top 15 HSTS Websites with highest max-age setting:</h3>
        <h4>(sorted by MaxAge)</h4>
        <table>
            <tr>
                <td>AlexaRank</td>
                <td>Hostname</td>
                <td>MaxAge</td>
                <td>IncludeSubdomains</td>
            </tr>
            <?  $sql = 'SELECT AlexaRank, Hostname, MaxAge, IncludeSubdomains FROM HstsResult WHERE Https=1 AND HasHeader=1 AND MaxAge>0 ORDER BY MaxAge DESC, AlexaRank ASC LIMIT 15';
            foreach ($pdo->query($sql) as $row) { ?>
                <tr>
                    <td class="number"><?=$row['AlexaRank'] ?></td>
                    <td><?=$row['Hostname'] ?></td>
                    <td class="number"><?=$row['MaxAge'] ?></td>
                    <td class="number"><?=$row['IncludeSubdomains'] ?></td>
                </tr>
            <?  } ?>
        </table>
    </div>

    <div class="tablebox">
        You can <a href="/download/results-2013-11-24.csv" target="_blank">download the results as CSV file</a> and download the used <a href="/download/top-1m.csv" target="_blank">Alexa Top 1M CSV</a>.<br><br>
        You can find this website and the crawler on GitHub: HSTS-Checker.<br><br>
        It's interesting that many big players don't have HSTS, but they are on the <a href="http://www.chromium.org/sts" target="_blank">Chromium and Firefox preloaded HSTS lists</a>.<br><br>
        The check has only been run once, so if a server was down during that test, it has been marked as "not HTTPS capable".<br><br>
        Only 999937 domains have been analyzed because there are entries in the Alexa list which are not just domains, for example "youtube.com/user/FIFAMONSTERz".<br><br>
        A modified version of <a href="http://www.chartjs.org/" target="_blank">Chart.js</a> has been used to draw the charts.<br><br>
        You can find a <a href="http://www.phpgangsta.de/hsts-http-strict-transport-security-hasts-schon" target="_blank">german blogpost about this in my PHP blog</a>.
    </div>
    <div style="clear: both;">
        Copyright 2013 by Michael Kliewe - http://www.phpgangsta.de
    </div>

    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
    <script src="/js/chart.js"></script>
    <script src="/js/script.js"></script>
</body>
</html>