<?php

require 'database.php';

$lat1 = null;
$long1 = null;
$lat2 = null;
$long2 = null;
$addFID = null;
$addCFID = null;

$sql1 = "select `fID_Art`, `Cited_ID_Art`  from `cited_paper`";
$result1 = mysqli_query($link, $sql1);

if (!$result1) {
    echo "\nDB Error, could not query the database Cited_Paper\n";
    echo 'MySQL Error: ' . mysqli_error($link);
    exit;
}

while ($row = mysqli_fetch_assoc($result1)) {

    $addFID  = $row['fID_Art'];
    $addCFID  = $row['Cited_ID_Art'];


    $add1 = null;
    $add2 = null;

    $sql2 = "SELECT `id_art`,`latlong` FROM `addresses` where `id_art`= $addFID ";
    $result2 = mysqli_query($link, $sql2);
    while ($row = mysqli_fetch_assoc($result2)) {
        $add1  = $row['latlong'];
    }
    if (!$result2) {
        echo "\nDB Error, could not query the Addresses database\n";
        echo 'MySQL Error: ' . mysqli_error($link);
        //exit;
    }

    $sql3 = "SELECT `Cited_ID_Art`,`latlong` FROM `cited_paper_address` where `id_art`= $addCFID ";
    $result3 = mysqli_query($link, $sql3);
    while ($row = mysqli_fetch_assoc($result3)) {
        $add2  = $row['latlong'];
    }
    if (!$result3) {
        echo "\nDB Error, could not query the Cited_paper_Addresses database\n";
        echo 'MySQL Error: ' . mysqli_error($link);
        //exit;
    }

    $lat1 = null;
    $lat2 = null;
    $lng1 = null;
    $lng2 = null;

    $sql4 = "SELECT `lat`,`long` FROM `final_addresses` where `id`= $add1 ";
    $result4 = mysqli_query($link, $sql4);
    while ($row = mysqli_fetch_assoc($result4)) {
        $lat1  = $row['lat'];
        $lng1  = $row['long'];
    }

    $sql5 = "SELECT `lat`,`long` FROM `final_addresses` where `id`= $add2 ";
    $result5 = mysqli_query($link, $sql5);
    while ($row = mysqli_fetch_assoc($result5)) {
        $lat2  = $row['lat'];
        $lng2  = $row['long'];
    }
    $FlyingDistance = distance($lat1, $lng1, $lat2, $lng2, "K"); //Distance in kilometers
    $time = timeCal($FlyingDistance);   // Time in minutes
    $FlyingTime  = display($time);      // Time Foramt in HH:MM:SS

    $queryFinal = "INSERT INTO `cited_paper` ( `FlyingDistance`, `FlyingTime`) VALUES ($FlyingDistance,$FlyingTime)";
    $result = mysqli_query($link, $queryFinal);
}



function distance($lat1, $lon1, $lat2, $lon2, $unit) {
    $theta = $lon1 - $lon2;
    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
    $dist = acos($dist);
    $dist = rad2deg($dist);
    $miles = $dist * 60 * 1.1515;
    $unit = strtoupper($unit);

    if ($unit == "K") {
        return ($miles * 1.609344);
    } else if ($unit == "N") {
        return ($miles * 0.8684);
    } else {
        return $miles;
    }
}

function display($seconds) {
    $t = round($seconds);
    return sprintf('%02d:%02d:%02d', ($t/3600),($t/60%60), $t%60);
}

function timeCal($distance){
     $speed = 885;           //747 Jumbo Jet assumption http://hypertextbook.com/facts/2002/JobyJosekutty.shtml
     $time = ($distance/$speed);
     return $time*60;
}
