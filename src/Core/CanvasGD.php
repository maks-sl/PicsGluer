<?php

namespace Core;


class CanvasGD
{
    /** @var $imagesGD ImageGD[] */
    private $imagesGD;
    private $minW;
    private $minH;
    private $destW;
    private $destH;
    private $pairHeights;
    private $numImages;
    private $numRows;
    private $topOffset;

    public function __construct(array $imagesGD, int $minW, int $minH, $topOffset = 0)
    {
        $this->imagesGD = $imagesGD;
        $this->numImages = count($imagesGD);
        $this->numRows = $numRows = round(count($imagesGD) / 2, 0, PHP_ROUND_HALF_UP);
        $this->minW = $minW;
        $this->minH = $minH;
        $this->topOffset = $topOffset;
    }

    private function configure()
    {
        // рассчет размеров холста
        $this->pairHeights = [];
        $rowsWidths = [$this->minW];
        foreach ($this->imagesGD as $key => $pasteIt) {
            if ($key % 2 == 0) {
                if (isset($this->imagesGD[$key + 1])) {
                    $this->pairHeights[] = max($pasteIt->height, $this->imagesGD[$key + 1]->height);
                } else {
                    $this->pairHeights[] = $pasteIt->height;
                }
                $rowsWidths[] = $pasteIt->width;
            } else {
                $rowsWidths[count($rowsWidths)-1] += $pasteIt->width;
            }
        }
        $this->destW = max($rowsWidths);
        $this->destH = max(array_sum($this->pairHeights), $this->minH);
    }

    public function generate(bool $centeredLast = false, $minSpace = false)
    {
        $this->configure();

        //увеличение
        if ($minSpace) {
            $minImageNeedWidth = (int)($this->destW / 2 * $minSpace);
            foreach ($this->imagesGD as $key => $pasteIt) {
                $scaleRatio = $minImageNeedWidth/$pasteIt->width;
                if ($scaleRatio > 1 ) {
                    $pasteIt->scale($scaleRatio);
                }
            }
            $this->configure();
        }

        $topOffset = ($this->destH - array_sum($this->pairHeights) > $this->topOffset)
            ? 0 : $this->topOffset;

        // создание холста
        $canvas = imagecreatetruecolor($this->destW, $this->destH + $topOffset);
        $whiteBg = imagecolorallocate($canvas, 255, 255, 255);
        imagefill($canvas, 0, 0, $whiteBg);

        // обход изображений
        foreach ($this->imagesGD as $key => $pasteIt) {
            $xy = $this->calcDestinationPosition($pasteIt, $key, $centeredLast);
            imagecopy(
                $canvas, $pasteIt->resource,
                $xy[0], $xy[1] + $topOffset,
                0, 0,
                $pasteIt->width, $pasteIt->height
            );
        }
        return $canvas;
    }

    private function calcDestinationPosition(ImageGD $image, int $position, $centered = false) : array
    {
        $currentRow = (int)round($position / 2, 0, PHP_ROUND_HALF_DOWN);
        $currentCol = $position % 2;

        $boxWidth = (
            $centered &&
            ($position == $this->numImages - 1) &&
            $currentCol == 0) ? $this->destW : $this->destW / 2;
        $diffX = $boxWidth - $image->width;
        $marginX = $diffX / 2;
        $startX = $boxWidth * $currentCol;

        $needHeight = $this->pairHeights[$currentRow];
        $boxHeight = $this->destH / $this->numRows;
        $diffY = $boxHeight - $needHeight;
        $marginY = $diffY / 2 + ($needHeight - $image->height);
        $startY = (array_sum($this->pairHeights) < $this->minH)
            ? $boxHeight * $currentRow
            : array_sum(array_slice($this->pairHeights, 0, $currentRow));

        return [round($startX + $marginX), round($startY + $marginY)];
    }
}