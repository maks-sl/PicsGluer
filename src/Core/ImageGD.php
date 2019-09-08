<?php

namespace Core;

/**
 * Class ImageGD
 *
 * @property string $systemPath путь к изображению на диске
 * @property resource $resource gd-ресурс изображения
 * @property int $width ширина изображения
 * @property int $height высота изображения
 *
 */

class ImageGD
{
    public $systemPath;
    public $resource;
    public $width;
    public $height;

    public function __construct(string $path)
    {

        $this->systemPath = $path;
        $sizes = getimagesize($this->systemPath);
        $this->width = $sizes[0];
        $this->height = $sizes[1];

        $resource = imagecreatefromjpeg($this->systemPath);
        if(!$resource) // Если не удалось
        {
            /* Создаем пустое изображение */
            $resource  = imagecreatetruecolor(150, 30);
            $bgc = imagecolorallocate($resource, 255, 255, 255);
            imagefilledrectangle($resource, 0, 0, 150, 30, $bgc);
        }
        $this->resource = $resource;
    }

    public static function makeSelfArray(array $paths) : array
    {
        return array_map(
            function (string $path) {
                return new self($path);
            }, $paths);
    }

    public function scale($scaleRatio) : void
    {
        $newWidth = (int)$this->width*$scaleRatio;
        $newResource = imagescale($this->resource, $newWidth);
        $this->resource = $newResource;
        $this->width = $newWidth;
        $this->height = (int)$this->height*$scaleRatio;
    }
}