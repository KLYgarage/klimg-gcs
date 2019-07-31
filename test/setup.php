<?php
use Klimg\GCS\UploadManager;
use Dotenv\Dotenv;

include __DIR__ . '/../vendor/autoload.php';

$dotenv = new Dotenv(__DIR__);
$dotenv->load();
$dotenv->required(['GCS_ENDPOINT', 'GCS_KEY', 'GCS_PUBLIC_PREFIX']);

$manager = new UploadManager(
    getenv('GCS_ENDPOINT'),
    getenv('GCS_KEY'),
    getenv('GCS_PUBLIC_PREFIX')
);

$manager->add(array(
    __DIR__ . '/images/one.jpg',
    __DIR__ . '/images/two.jpg',
    __DIR__ . '/images/three.jpg'
), 'test4');

$resps = $manager->send();

foreach ($resps as $resp) {
    echo (string) $resp . "\n";
}

var_dump($resps);
