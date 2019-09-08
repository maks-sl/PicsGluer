<?php

use Core\FileHelper;
use Core\ImageGD;
use Core\CanvasGD;

require "vendor/autoload.php";

const INPUT_FILENAME = "input.txt";
const GLUED_MIN_WIDTH = 700;
const GLUED_MIN_HEIGHT = 480;
const IMAGES_DIR = "f";
const GLUED_DIR = "out-f";

$fileHelper = new FileHelper(__DIR__);
$codes = $fileHelper->readInputFile(INPUT_FILENAME);

echo "Gluing projects facades started".PHP_EOL;
foreach ($codes as $code) {
    echo $code;
    try {
        $realPaths = $fileHelper->makeFacadeRealPaths(IMAGES_DIR, $code);
        $canvasGD = new CanvasGD(ImageGD::makeSelfArray($realPaths), GLUED_MIN_WIDTH, GLUED_MIN_HEIGHT);
        $fileHelper->savePngAndDestroy($canvasGD->generate(false,0.85),GLUED_DIR, $code);
    }
    catch (LogicException $e) {echo " ... ".$e->getMessage().PHP_EOL; continue;}
    echo " ... DONE".PHP_EOL;
    unset($canvasGD);
}