<?php
namespace Keboola\Processor\EscapeCharacter;

use Keboola\Csv\CsvFile;

/**
 * @param \SplFileInfo $sourceFile
 * @param $destinationFolder
 * @param $delimiter
 * @param $enclosure
 * @param $escapedBy
 */
function processFile(\SplFileInfo $sourceFile, $destinationFolder, $delimiter, $enclosure, $escapedBy)
{
    $sourceCsv = new CsvFile($sourceFile->getPathname(), $delimiter, $enclosure, $escapedBy);
    $destinationCsv = new CsvFile($destinationFolder . $sourceFile->getFilename(), $delimiter, $enclosure);
    $re = '/' . preg_quote($escapedBy) . '(.)/mU';
    foreach ($sourceCsv as $index => $row) {
        $row = array_map(function ($value) use ($re) {
            return preg_replace($re, '$1', $value);
        }, $row);
        $destinationCsv->writeRow($row);
    }
}
