<?php
require "app/Config/conf.inc.php";
require "app/Core/Autoloader.php";
require "app/Lib/dev.conf.php";

use Songfolio\Core\View;
use Songfolio\Core\Autoloader;
use Songfolio\Core\Routing;
use Songfolio\Models\Contents;
use Songfolio\Models\Events;

$autoloader = new Autoloader();
$autoloader->addNamespace('Songfolio', 'app');
$autoloader->register();

session_start();

$route = Routing::getRoute();

if(!empty($route)){
    extract($route);

    $container = [];
    $container += require 'app/Config/di.global.php';


    if (file_exists($controllerPath)) {
        include $controllerPath;

        if(class_exists('\\Songfolio\\Controllers\\' . $controller)){

            $controllerObject = $container['Songfolio\\Controllers\\'.$controller]($container);

            if(method_exists($controllerObject, $action)){
                $controllerObject->$action();
            }else{
                View::show404("L'action ".$action." n'existe pas.");
            }
        }else{
            View::show404("La class ".$controller." n'existe pas.");
        }
    }else{
        View::show404("Le fichier controller ".$controller." n'existe pas.");
    }
}elseif( $content = Contents::getBySlug( Routing::currentSlug(true) ) ){
    $content->show();
}elseif( $event = Events::getBySlug( Routing::currentSlug(true) ) ){
    $event->show();
}else{
    View::show404("L'url n'existe pas.");
}