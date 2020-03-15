<?php
error_reporting(E_ERROR);

require_once('includes/functions.php');
require_once('includes/Database.php');
require_once('includes/Table.php');
require_once('includes/Masker.php');

$availableOptions = 'u:p:d:h:P:s:f:U:';

$cliOptions = getopt($availableOptions);
$masker = new Masker($cliOptions);

$masker->getRules();
