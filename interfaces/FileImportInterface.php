<?php

namespace app\interfaces;

/**
 * Interface FileImportInterface
 * @package app\interfaces
 */
interface FileImportInterface
{
    /**
     * @param string $filename
     */
    public function setFile(string $filename): void;

    /**
     * @return bool
     */
    public function execute(): bool;

    /**
     * Results:
     * ```
     *  [
     *    'total' => 0,
     *    'categorise' => 0,
     *    'offers' => 0,
     *  ];
     * ```
     * @return array
     */
    public function getResults(): array;
}
