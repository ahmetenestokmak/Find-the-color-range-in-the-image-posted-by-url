<?php
$post = (json_decode(file_get_contents('php://input'), JSON_UNESCAPED_UNICODE));
$imagePath = $post["url"];
$path = explode(".", $imagePath);

$tip = $path[count($path) - 1];
if ($tip == "png") $image = imagecreatefrompng($imagePath);
else if ($tip == "jpg" || $tip == "jpeg") $image = imagecreatefromjpeg($imagePath);
else if ($tip == "gif") $image = imagecreatefromgif($imagePath);

$width = imagesx($image);
$height = imagesy($image);
$data = array();
for ($x = 0; $x < $width; $x++) {
    for ($y = 0; $y < $height; $y++) {
        $px = imagecolorat($image, $x, $y);
        $r = ($px & 0xFF0000) >> 16;
        $g = ($px & 0x00FF00) >> 8;
        $b = ($px & 0x0000FF);
        $rgb = $r . '_' . $g . '_' . $b;
        if (isset($data[$rgb])) $data[$rgb] += 1;
        else $data[$rgb] = 1;
    }
}

arsort($data);
$i=0;
foreach ($data as $color => $piece) {
    $part = explode('_', $color);
    if (strlen(dechex($part[0])) < 2) $hex1 = '0' . dechex($part[0]);
    else $hex1 = dechex($part[0]);
    if (strlen(dechex($part[1])) < 2) $hex2 = '0' . dechex($part[1]);
    else $hex2 = dechex($part[1]);
    if (strlen(dechex($part[2])) < 2) $hex3 = '0' . dechex($part[2]);
    else $hex3 = dechex($part[2]);
    $result->$i->ColorCode = strtoupper($hex1 . $hex2 . $hex3);
    $result->$i->ColorRate = (100/array_sum($data))*$piece;
    $i++;
}
echo (json_encode($result, JSON_UNESCAPED_UNICODE));