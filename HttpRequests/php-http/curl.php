<?php

declare(strict_types=1);

require_once('vendor/autoload.php');

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// ... constants, the call to http_build_query, and definition of $requestUri, and $queryString

$ch = curl_init();
$requestUri = '';
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_URL => $requestUri,
    CURLOPT_SSH_COMPRESSION => true,
]);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_URL => $requestUri
]);
$result = curl_exec($ch);
$photoData = unserialize($result);

curl_close($ch);

foreach ($photoData['photos']['photo'] as $photoDatum) {
    printf("Downloading %s.jpg\n", $photoDatum['title']);
    $file_url = sprintf(
        PHOTO_URL,
        $photoDatum['server'],
        $photoDatum['id'],
        $photoDatum['secret']
    );
    $destination_path = PHOTOS_DIR . '/' . $photoDatum['title'] . '.jpg';

    $fp = fopen($destination_path, "w");
    $ch = curl_init($file_url);
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_exec($ch);
    $st_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    fclose($fp);
    printf("  Downloaded %s.jpg\n", $photoDatum['title']);
}
