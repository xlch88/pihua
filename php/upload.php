<?php
$pwd = include('../../upload_pwd.php');

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
	die('Method Not Allowed');
}

if(!isset($_FILES['file'])) {
	die('File not found');
}

if(!isset($_SERVER['HTTP_AUTHORIZATION']) || $_SERVER['HTTP_AUTHORIZATION'] !== 'Bearer ' . $pwd){
	die('Unauthorized');
}


$file = $_FILES['file'];
if ($file['error'] !== UPLOAD_ERR_OK) {
	die('Upload error');
}

$tmpZip = $file['tmp_name'];
$zip = new ZipArchive();
if ($zip->open($tmpZip) !== true) {
	die('Failed to open zip');
}

// Basic zip-slip checks
for ($i = 0; $i < $zip->numFiles; $i++) {
	$entry = $zip->getNameIndex($i);
	if ($entry === '' || strpos($entry, '..') !== false || $entry[0] === '/' || $entry[0] === '\\' || preg_match('/^[A-Za-z]:/', $entry)) {
		$zip->close();
		die('Invalid zip entries');
	}
}

$base = dirname(dirname(__DIR__));
$public = $base . DIRECTORY_SEPARATOR . 'public';
$public_tmp = $base . DIRECTORY_SEPARATOR . 'public_tmp';
$public_old = $base . DIRECTORY_SEPARATOR . 'public_old';

function rrmdir($dir) {
	if (!is_dir($dir)) return;
	$it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
	$files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
	foreach ($files as $f) {
		if ($f->isDir()) {
			rmdir($f->getPathname());
		} else {
			unlink($f->getPathname());
		}
	}
	rmdir($dir);
}

// Ensure clean tmp dir
if (is_dir($public_tmp)) {
	rrmdir($public_tmp);
}
if (!mkdir($public_tmp, 0755, true) && !is_dir($public_tmp)) {
	$zip->close();
	die('Failed to create temp dir');
}

// Extract
if (!$zip->extractTo($public_tmp)) {
	$zip->close();
	rrmdir($public_tmp);
	die('Failed to extract zip');
}
$zip->close();

// Prepare public_old
if (is_dir($public_old)) {
	rrmdir($public_old);
}

// Rename public -> public_old (if exists), then public_tmp -> public
if (is_dir($public)) {
	if (!rename($public, $public_old)) {
		rrmdir($public_tmp);
		die('Failed to rename public to public_old');
	}
}

if (!rename($public_tmp, $public)) {
	// try rollback
	if (is_dir($public_old) && !is_dir($public)) {
		@rename($public_old, $public);
	}
	rrmdir($public_tmp);
	die('Failed to promote new public');
}

echo 'OK';