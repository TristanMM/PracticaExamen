<?php
$container->set('config_bd',function(){
    return(object)[
        "host"=>"db",
        "db"=> "biblio",
        "usr"=> "root",
        "passw"=>"123456",
        "charset"=> "utf8mb4"
    ];
}
);