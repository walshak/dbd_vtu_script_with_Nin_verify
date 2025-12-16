<?php
$lines = file('storage/logs/laravel.log');
foreach($lines as $line) {
    if(strpos($line, 'API Request Headers') !== false) {
        echo $line;
    }
}
