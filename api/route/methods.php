<?php

require __DIR__ . '/../credentials.php';

function entriesInPeriod($from, $to) {
    $link = new mysqli('localhost', $GLOBALS["ROADTOMORTI_mysql_user"], $GLOBALS["ROADTOMORTI_mysql_pass"], $GLOBALS["ROADTOMORTI_mysql_db"]) or die ('Die');
    mysqli_set_charset($link, "UTF8");

    $query = 'SELECT * FROM entries WHERE date BETWEEN DATE_SUB("' . $from . '", INTERVAL 1 DAY) AND "' . $to . '"';
    $result = mysqli_query($link, $query);

    $lastRow = null;
    $table = array();
    while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
        if ($lastRow != null) {
            $diff = $row[1] - $lastRow;
            $date = $row[0];
            $table[] = [$date, $diff];
        }
        $lastRow = $row[1];
    }

    return $table;
}

?>