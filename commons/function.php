<?php

function debug($data)
{
    echo "<pre style='background-color: #080808;color: #fff;font-weight: 100;line-height: 20px;'><br />";
    print_r($data);
    echo "<br /></pre>";
    die;
}


function pr($data){
    echo json_encode($data);
    die;
}

function sprint($data){
    echo "<pre><br />";
    print_r($data);
    echo "<br /></pre>";
}
