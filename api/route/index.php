<?php

require __DIR__ . '/methods.php';

$from = $_GET["from"];
$to = $_GET["to"];

header('Content-Type: application/json;charset=utf-8');
$entries = entriesInPeriod($from, $to);
$firstRow = true;

print '{"entries": [';
foreach ($entries as $entry) {
	if ($firstRow == false) {
		print(', ');
	}
	print('{"date": "' . $entry[0] .'", "wordsWritten": ' . $entry[1] . '}');
	$firstRow = false;
}
print ']}';

?>