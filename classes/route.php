<?php
namespace Classes;

use Classes\myBlogController;

class Route{

    const RouteTable = [
            ['url' => '/category',	'class' => myBlogController::class, 'module' => 'category'],
            ['url' => '/article',	'class' => myBlogController::class, 'module' => 'article'],
            ['url' => '/main', 	'class' => myBlogController::class, 'module' => 'main']
        ];

    const RT404 = ['url' => '/404', 'class' => myBlogController::class, 'module' => 'p404'];


    public static function getRouteData($url){
        if($url == '/'){
            $url = '/main';
        }
        $data = null;
        foreach(self::RouteTable as $item){
            if(preg_match('/('.preg_quote($item['url'],'/').')(.*)/',$url,$result)){
                $data = [
                    'action' => $result[1],
                    'class' => $item['class'],
                    'module' => $item['module'],
                    'params' => $result[2]
                ];
                break;
            };
        }
        if(!isset($data)){
            $data = ['action' => self::RT404['url'],
                'class' => self::RT404['class'],
                'module' => self::RT404['module'],
                'params' => ''];
        }
        return $data;
    }
}

?>