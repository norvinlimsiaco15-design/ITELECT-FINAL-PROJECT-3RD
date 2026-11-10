<?php

$sizes = [16, 32, 48, 96, 180, 192];
$dir = __DIR__.'/../public/favicons';
if (! is_dir($dir)) {
    mkdir($dir, 0777, true);
}

$pngs = [];
foreach ($sizes as $size) {
    $png = renderPng($size);
    $name = match ($size) {
        180 => 'apple-icon-180x180.png',
        192 => 'android-icon-192x192.png',
        default => "favicon-{$size}x{$size}.png",
    };
    file_put_contents($dir.'/'.$name, $png);
    if (in_array($size, [16, 32, 48], true)) {
        $pngs[$size] = $png;
    }
}

file_put_contents(__DIR__.'/../public/favicon.ico', buildIco($pngs));
file_put_contents($dir.'/favicon.ico', buildIco($pngs));
echo "favicons written\n";

function renderPng(int $size): string
{
    $im = imagecreatetruecolor($size, $size);
    imagesavealpha($im, true);
    imagealphablending($im, true);
    $green = imagecolorallocate($im, 27, 58, 47);
    $soil = imagecolorallocate($im, 107, 63, 36);
    $sand = imagecolorallocate($im, 196, 161, 90);
    $rock = imagecolorallocate($im, 215, 221, 227);
    $track = imagecolorallocate($im, 226, 182, 87);
    $dark = imagecolorallocate($im, 14, 36, 28);
    $white = imagecolorallocate($im, 244, 247, 245);

    imagefilledrectangle($im, 0, 0, $size, $size, $green);
    $s = $size / 128;
    imagefilledellipse($im, (int) (46 * $s), (int) (78 * $s), (int) (70 * $s), (int) (36 * $s), $soil);
    imagefilledellipse($im, (int) (40 * $s), (int) (82 * $s), (int) (48 * $s), (int) (22 * $s), $sand);
    $poly = [
        (int) (78 * $s), (int) (52 * $s),
        (int) (104 * $s), (int) (90 * $s),
        (int) (58 * $s), (int) (90 * $s),
    ];
    imagefilledpolygon($im, $poly, $rock);
    imagefilledrectangle($im, (int) (16 * $s), (int) (98 * $s), (int) (112 * $s), (int) (108 * $s), $dark);
    imagefilledrectangle($im, (int) (16 * $s), (int) (98 * $s), (int) (78 * $s), (int) (108 * $s), $track);
    imagefilledellipse($im, (int) (78 * $s), (int) (103 * $s), (int) (14 * $s), (int) (14 * $s), $white);

    ob_start();
    imagepng($im);
    imagedestroy($im);

    return ob_get_clean();
}

function buildIco(array $pngs): string
{
    $count = count($pngs);
    $header = pack('vvv', 0, 1, $count);
    $offset = 6 + (16 * $count);
    $entries = '';
    $data = '';
    foreach ($pngs as $size => $png) {
        $entries .= pack('CCCCvvVV', $size, $size, 0, 0, 1, 32, strlen($png), $offset);
        $offset += strlen($png);
        $data .= $png;
    }

    return $header.$entries.$data;
}
