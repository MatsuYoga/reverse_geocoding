<?
// comunicação DB
    $link = mysqli_connect("localhost", "root", "root", "geocode");

    if (!$link) {
        die('Could not connect: ' . mysqli_error() . mysqli_connect_error());
    }

?>