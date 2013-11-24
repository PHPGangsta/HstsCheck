PHPGangsta HSTS Checker
=====================

* Copyright (c) 2013, [http://www.phpgangsta.de](http://www.phpgangsta.de)
* Author: Michael Kliewe, [@PHPGangsta](http://twitter.com/PHPGangsta)
* Licensed under the BSD License.


This script checks the Alexa Top 1 Million websites if they support HTTPS and if they have the "Strict-Transport-Security"-Header.
The results are written to a database, see table.sql for the schema.

The crawler has been written by using [pthreads](http://pthreads.org), to be able to parallelize it, otherwise it would take days to crawl 1 million URLS.

You can download the results as CSV file from here: http://hstscheck.phpgangsta.de/results_2013-11-24.csv.zip
You can see some statistics at http://hstscheck.phpgangsta.de or (german): http://www.phpgangsta.de/hsts-http-strict-transport-security-hasts-schon

Installation
------

Create the database schema and set the database login credentials.


Usage
------

Start check.php: php check.php


Notes
------

If you like this script or have some features to add, contact me, fork this project, send pull requests, you know how it works.
