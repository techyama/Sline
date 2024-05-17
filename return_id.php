<?php

function ret_user_id($profile_id){
    require("db_connect.php");
    $sql = "SELECT user_id FROM profile WHERE profile_id = :profile_id";
    $stm = $pdo -> prepare($sql); 
    $stm -> bindvalue(":profile_id",$profile_id,PDO::PARAM_INT);
    if($stm->execute()){
        $result = $stm->fetchAll(PDO::FETCH_ASSOC);
        return $result[0]["user_id"];
    }
}

function ret_profile_id($user_id){
    require("db_connect.php");
    $sql = "SELECT profile_id FROM profile WHERE user_id = :user_id";
    $stm = $pdo -> prepare($sql); 
    $stm -> bindvalue(":user_id",$user_id,PDO::PARAM_STR);
    if($stm->execute()){
        $result = $stm->fetchAll(PDO::FETCH_ASSOC);
        return $result[0]["profile_id"];
    }
}
?>