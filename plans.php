<?php

use Core\CanvasGD;
use Core\FileHelper;
use Core\ImageGD;

require "vendor/autoload.php";

const INPUT_FILENAME = "input.txt";
const IMAGES_DIR = "pl";
const GLUED_DIR = "out-pl";
const GLUED_MIN_WIDTH = 700;
const GLUED_MIN_HEIGHT = 700;

$fileHelper = new FileHelper(__DIR__);
$codes = $fileHelper->readInputFile(INPUT_FILENAME);

echo "Gluing projects plans started".PHP_EOL;
foreach ($codes as $code) {
    echo $code;
    try {
        $realPaths = $fileHelper->makePlansRealPaths(IMAGES_DIR, $code);
        $canvasGD = new CanvasGD(ImageGD::makeSelfArray($realPaths), GLUED_MIN_WIDTH, GLUED_MIN_HEIGHT, 40);
        $fileHelper->savePngAndDestroy($canvasGD->generate(true),GLUED_DIR, $code);
    }
    catch (LogicException $e) {echo " ... ".$e->getMessage().PHP_EOL; continue;}
    echo " ... DONE".PHP_EOL;
    unset($canvasGD);
}