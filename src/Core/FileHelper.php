<?php

namespace Core;

class FileHelper
{
    private $rootDir;

    public function __construct($rootDir)
    {
        $this->rootDir = $rootDir;
    }

    public function readInputFile($inputFile) : array
    {
        return file($this->rootDir . DIRECTORY_SEPARATOR . $inputFile, FILE_IGNORE_NEW_LINES);
    }

    public function makeFacadeRealPaths(string $facadeDir, string $code) : array
    {
        // пути к реальным файлам
        $result = [];
        for ($fn = 1; $fn <= 4; $fn++) {
            $template = $code . '_f' . $fn;
            $path = $this->rootDir .
                DIRECTORY_SEPARATOR . $facadeDir .
                DIRECTORY_SEPARATOR . $template . '.jpg';
            if (file_exists($path)) {
                $result[] = $path;
            }
        }
        return $result;
    }

    public function makePlansRealPaths(string $plansDir, string $code) : array
    {
        $templates = [$code . '_c'];
        for ($fn = 1; $fn <= 4; $fn++) {
            $templates[] = $code . '_' . $fn;
        }
        $templates[] = $code . '_m';

        $result = [];
        foreach ($templates as $template) {
            $path = $this->rootDir .
                DIRECTORY_SEPARATOR . $plansDir .
                DIRECTORY_SEPARATOR . $template . '.jpg';
            if (file_exists($path)) {
                $result[] = $path;
            }
        }
        if (count($result) == 0 ) {
            throw new \LogicException("Images not found!");
        }
        return $result;
    }

    public function savePngAndDestroy($resource, string $gluedDir, string $fileName) : bool
    {
        $newName = $this->rootDir .
            DIRECTORY_SEPARATOR . $gluedDir .
            DIRECTORY_SEPARATOR . $fileName . '.png';
        if (!$result = imagepng($resource, $newName)){
            throw new \LogicException("Saving image error!");
        }
        imagedestroy($resource);
        return $result;
    }
}