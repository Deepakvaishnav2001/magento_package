<?php

namespace Sharika\CustomerImport\Api;

use Symfony\Component\Console\Input\InputInterface;

interface ImportInterface
{
    public const PROFILE_NAME = "profile";
    public const FILE_PATH = "filepath";

    /**
     * Get import customer data
     *
     * @param InputInterface $input
     * @return array
     */
    public function getImportData(InputInterface $input): array;

    /**
     * Reads the input data
     *
     * @param string $data
     * @return array
     */
    public function readData(string $data): array;

    /**
     * Formats the data
     *
     * @param mixed $data
     * @return array
     */
    public function formatData(mixed $data): array;
}
