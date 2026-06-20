<?php

session_start();

if(!isset($_SESSION['nid']))
{
    header("Location: login.php");
    exit();
}

echo "<h2>Patient Dashboard</h2>";

echo "Welcome : ".$_SESSION['nid'];

echo "<br><br>";

echo "<a href='logout.php'>Logout</a>";