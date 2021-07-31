<?php

require __DIR__ . '/methods.php';

$wordCount = countWords();
logWords($wordCount);

header('Content-Type: application/json;charset=utf-8');
print('{ "num_words": ' . $wordCount . ' }');

?>
