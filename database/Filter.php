<?php
header("Content-Type: text/html;charset=utf-8");
set_time_limit(0);
/**
 * Created by PhpStorm.
 * User: india
 * Date: 4/18/2015
 * Time: 5:51 PM
 */


require 'database.php';
$rowid = null;
$k=1;

for($index=1;$index<30000;$index=$index+100){
//$index = 2;
    $query2 = 'SELECT * FROM `address_unique` where `latlongid` is NULL  Limit '.($index*1).", 100 ";
    $result2 = mysqli_query($link, $query2);
    //echo $result2->num_rows."<br/>";
    if($result2->num_rows>0){
    while ($row = mysqli_fetch_assoc($result2)) {
        $idN  = $row['institute'];
            $id = $row['id'];
            $inst = $row['institute'];
            $ville = $row['ville'];
            $province = $row['province'];
            $Country = $row['Country'];
               // echo "<br/>".$inst."------<br/>";
                $query1 = "SELECT * FROM `address_unique` where `latlongid` is NOT NULL and `ville` = \"$ville\" and `province` = \"$province\" and `Country` = \"$Country\" ";
                $result1 = mysqli_query($link, $query1);
                echo "<br />".$query1;
               // echo $result1->num_rows;
                if($result1->num_rows>0) {
                    while ($row = mysqli_fetch_assoc($result1)) {
                        if ($id != $row['id'] && $inst != null && $row['institute'] != null) {

                            $length1 = strlen($inst);
                            $length2 = strlen($row['institute']);
                            $str1 = strtolower(substr($inst,0,$length1/2));
                            $str2 = strtolower(substr($row['institute'],0,$length2/2));
                            //echo $str1 ." = " . $row['institute']." ====== ".$str2 . " = " . $inst . "<br/>";

                                if (stristr($inst, $row['institute']) || stristr($row['institute'], $inst)) {
                                    echo $row['id'] . " <- Level 1 match------".$k."------------>  " . $row['institute'] . "     <-------------------->  " . $inst;
                                    echo "<br />";
                                    $newID = $row['latlongid'];
                                    $queryUpdate ="\n UPDATE `address_unique` set `latlongid` = $newID WHERE `id` = $id";
                                    $result9 = mysqli_query($link, $queryUpdate);
                                    $k++;
                                    break;
                                }elseif(stristr($str1, $str2) || stristr($str2, $str1)){
                                    echo $row['id'] . " <- Level 2 Match ------".$k."------------>  " . $str1 . "     <-------------------->  " . $str2;
                                    echo "<br />";
                                    $newID = $row['latlongid'];
                                    $queryUpdate ="\n UPDATE `address_unique` set `latlongid` = $newID WHERE `id` = $id";
                                    $result9 = mysqli_query($link, $queryUpdate);
                                    $k++;
                                    break;
                                }else{
                                   // echo "I am no where<br />";
                                   // break;
                                }
                        }
                    }
                }
        }
    }
    if (!$query2) {
        echo 'MySQL Error: ' . mysqli_error($link);
        exit;
    }
}
