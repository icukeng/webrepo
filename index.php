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
$app->run();