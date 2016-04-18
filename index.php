<?php
require 'vendor/autoload.php';
require 'debrepo.php';
require 'package.php';

$app = new \Slim\Slim(array(
	'debug' => true,
	'view' => new \Slim\Views\Twig(),
));
$view = $app->view();
$view->parserOptions = array(
	'debug' => true,
);
$view->parserExtensions = array( 
	new \Slim\Views\TwigExtension(),
	new \Twig_Extension_Debug()
);
// defining baseUrl
$view->getEnvironment()->addGlobal('baseUrl', $app->request->getScriptName());
// =======================================================
$SYSCONF = json_decode(file_get_contents('repos.json'), true);
$USRCONF = json_decode(file_get_contents('data.json'), true);
// =======================================================
$app->get('/', function () use($app, $SYSCONF, $USRCONF) {
	$registry = new PackageList();
	// loading packages
	foreach ($SYSCONF['repos'] as $params) {
		$r = new DebRepo($params['repo']);
		$list = $r->content();
		foreach ($list as $item)
			$registry->register( $params['repo'], $item['name'], $item['vers'], $item['arch']);
	}
	// loading labels
	$packages = $registry->find()->group(array('name'), 'name');
	$labeled_packages = array('default' => array());
	foreach ($packages as $name) {
		if( isset($USRCONF['maps'][$name]) )
			foreach ($USRCONF['maps'][$name] as $label)
				$labeled_packages[$label][$name] = $name;
		else
			$labeled_packages['default'][$name] = $name;
	}
	// for standart view need grouped 
	//   by name,
	//   by repo -> by vers then arch's
	// for version compare view needed
	//   by name 
	//   by vers -> by repo then arch's
	$version1 = $registry->find()->group(array('name', 'repo', 'version', 'arch'));
	$version2 = $registry->find()->group(array('name', 'version', 'repo', 'arch'));
	$versions = array();
	foreach ($version1 as $k => $v) $versions[$k]['repo'] = $v;
	foreach ($version2 as $k => $v) $versions[$k]['version'] = $v;
	$app->render('view.html', array(
		'repolist'  => $SYSCONF['repos'],  // classifier
		"labellist" => $USRCONF['labels'], // classifier
		'packages'  => $labeled_packages,
		"labels"    => $USRCONF['maps'],
		"versions"  => $versions,
	));
});

$app->post('/copy/', function () use($app, $SYSCONF) {
	$rx = $app->request->params('rx');
	$tx = $app->request->params('tx');
	$pkg  = $app->request->params('pkg');
	$vers = $app->request->params('vers');
	$t = (new Transfer(new DebRepo($tx), new DebRepo($rx)))->copy($pkg, $vers);
	if($t) echo 'OK';
	else   echo 'FAIL';
})->name('copy');
// creates job
$app->post('/execution', function() use ($app) {
	// write to .json file the name of file that needs to be builded
	$fp = fopen('job.json', 'w+');
	fwrite($fp, json_encode(['name' => $app->request->post('name')]));
	fclose($fp);
	return $app->response()->body(true);
});
// get execution status
$app->get('/status', function() use ($app) {
	$status = json_decode(file_get_contents('status.json'));
	$response = $app->response();
	$response['Content-Type'] = 'application/json';
	$response->status(200);
	$response->body(json_encode($status));
});
$app->run();