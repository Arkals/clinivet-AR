<?php

$basePath = '/clinivet';

if (isset($_GET['route'])) {
	$route = trim((is_array($_GET['route']) ? end($_GET['route']) : $_GET['route']), '/');
	if ($route === '' || preg_match('/\.php$/i', $route)) { $route = 'home'; }
	// Preserve other query params except 'route'
	$params = $_GET;
	unset($params['route']);
	$qs = http_build_query($params);
	$target = rtrim($basePath, '/') . '/' . $route;
	if ($qs) { $target .= '?' . $qs; }
	header('Location: ' . $target, true, 301);
	exit;
}

// 2) Redirect bare /.../index.php to /.../home
$uri = $_SERVER['REQUEST_URI'] ?? '';
$qs = $_SERVER['QUERY_STRING'] ?? '';
if ($qs === '' && preg_match('#/index\.php$#i', $uri)) {
	header('Location: ' . rtrim($basePath, '/') . '/home', true, 301);
	exit;
}

// Delegate handling to backend router
require __DIR__ . '/backend/public/index.php';
