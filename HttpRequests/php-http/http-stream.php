<?php


declare(strict_types=1);

require_once('vendor/autoload.php');

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

const BASE_URL = 'https://api.flickr.com/services';
const PHOTO_URL = 'https://live.staticflickr.com/%s/%s_%s_b.jpg';
const PHOTOS_DIR = 'photos';

$apiKey = $_ENV['API_KEY'];

$opts = [
    'https' => [
        'max_redirects' => 3,
    ],
];
$queryString = http_build_query([
    'api_key' => $apiKey,
    'content_type' => 1,
    'format' => 'php_serial',
    'media' => 'photos',
    'method' => 'flickr.photos.search',
    'per_page' => 10,
    'safe_search' => 1,
    'text' => 'Kakadu National Park'
]);
$requestUri = sprintf(
    '%s/rest/?%s',
    BASE_URL,
    $queryString
);
$fp = fopen($requestUri, 'r');

$photoData = unserialize(stream_get_contents($fp));

foreach ($photoData['photos']['photo'] as $photoDatum) {
    printf("Downloading %s.jpg\n", $photoDatum['title']);
    $photoFile = file_get_contents(
        sprintf(
            PHOTO_URL,
            $photoDatum['server'],
            $photoDatum['id'],
            $photoDatum['secret']
        )
    );
    file_put_contents(PHOTOS_DIR . '/' . $photoDatum['title'] . '.jpg', $photoFile);
    printf("  Downloaded %s.jpg\n", $photoDatum['title']);
}

fclose($fp);
