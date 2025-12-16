<?php
echo "<h1>PHP Test</h1>";
echo "<p>PHP is working: " . phpversion() . "</p>";
echo "<p>Current time: " . date('Y-m-d H:i:s') . "</p>";

$testVar = "Hello World";
echo "<p>Variable test: " . $testVar . "</p>";

$testArray = ['name' => 'Test User', 'balance' => 1000];
echo "<p>Array test: " . $testArray['name'] . " has ₦" . number_format($testArray['balance'], 2) . "</p>";
?>