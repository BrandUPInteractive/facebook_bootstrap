<?php

// Application Configuration
$config = array(
    'admin' => array(
	    'user' => 'admin',
	    'password' => '78z2w8arGuTcuKu'
    ),
    'basePath' => '/fb_app',
    'db' => array(
        'db.options' => array(
            'driver'   => 'pdo_mysql',
            'host'      => 'localhost',
            'dbname'    => 'database_name',
            'user'      => 'root',
            'password'  => 'zkWk8TtQYEQASJ2E74jhYBHxkg2bqr',
            'charset'   => 'utf8'
        )
    ),
    'timer.start' => FBBOOTSTRAP_START,
    'monolog' => array(
        'monolog.level' => \Monolog\Logger::DEBUG,
        'monolog.logfile' => __DIR__ . '/app.log'
    ),
    'twig' => array(
        'twig.path' => __DIR__ . '/views'
    )
);

// Create Silex Application Instance
$app = new Silex\Application(array('config' => $config));

// Debuging
$app['debug'] = true;

// Register Service Controller - to load dynamically controllers
$app->register(new Silex\Provider\ServiceControllerServiceProvider());

// Register Twig - templating engine
$app->register(new Silex\Provider\TwigServiceProvider(), $app['config']['twig']);

// Register Monolog - logging engine
$app->register(new Silex\Provider\MonologServiceProvider(), $app['config']['monolog']);

// Register Doctrine - database engine
$app->register(new Silex\Provider\DoctrineServiceProvider(), $app['config']['db']);


// Autoregister Controllers routes based on the following format:
// methodnameGET, methodnamePOST, methodnamePUT, methodnameDELETE, methodnameMATCH
$controllersRoutes = array(
	'General' => '/',
	'Admin' => '/admin',
	'Ajax' => '/ajax',
);

foreach ($controllersRoutes as $className => $routePath) {
	$fullClassName = 'Controllers\\' . $className;

	// Mount controller to be able to serve.
	$app['controllers.' . $className] = $app->share(function() use ($app, $fullClassName) {
	    return new $fullClassName($app);
	});
	
	$routes = call_user_func($fullClassName . '::getRoutes');

	// Mount the found routes to the path and link to the controller's method.
	$toMount = $app['controllers_factory'];
	foreach ($routes as $route) {
		$toMount->{$route['httpMethod']}($route['path'], 
			'controllers.' . $route['className'] . ':' . $route['classMethod']
		);
	}
	
	$app->mount($app['config']['basePath'] . $routePath, $toMount);
}

return $app;