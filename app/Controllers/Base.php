<?php

namespace Controllers;

class Base {
    
    protected $app;
    protected $twig;
    protected $monolog;
    protected $db;
    
    public function __construct($app) {

        $this->app = $app;
        $this->twig = $app['twig'];
        $this->monolog = $app['monolog'];
        $this->db = $app['db'];
        
    }
    
    public static function getRoutes() {
    	$classMethods = get_class_methods(get_called_class());
    	$className = str_replace(__NAMESPACE__ . '\\', '', get_called_class());
    	$paths = array();
    	
    	foreach ($classMethods as $classMethod) {
    		preg_match('/^([a-z0-9_]+)(GET|POST|PUT|MATCH|DELETE)$/i', $classMethod, $path);
    		if ($path)
    			array_push($paths, array(
    				'classMethod' => $path[0],
    				'path' => 'index' == $path[1] ? '/' : $path[1],
    				'httpMethod' => $path[2],
    				'className' => $className
    			));
    	}
    	
	    return $paths;
    }
    
}