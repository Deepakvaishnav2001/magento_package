<?php


namespace Sharika\CustomerImport\Model;

use Sharika\CustomerImport\Api\ImportInterface;
use Magento\Framework\File\Csv;
use Magento\Framework\Filesystem\Driver\File;
use Psr\Log\LoggerInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Sharika\CustomerImport\Model\Importer\CsvImporter;
use Sharika\CustomerImport\Model\Importer\JsonImporter;

class ProfileManagement
{
    /**
     * @var Csv
     */
    protected Csv $csv;

    /**
     * @var File
     */
    private File $file;
    
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;

    /**
     * ProfileManagement constructor
     *
     * @param File $file
     * @param Csv $csv
     * @param LoggerInterface $logger
     * @param SerializerInterface $serializer
     */
    public function __construct(
        File $file,
        Csv $csv,
        LoggerInterface $logger,
        SerializerInterface $serializer
    ) {
        $this->csv = $csv;
        $this->file = $file;
        $this->logger = $logger;
        $this->serializer = $serializer;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param string $type
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function create(string $type): ImportInterface
    {
        if ($type === "csv") {
            return new CsvImporter(
                $this->file,
                $this->csv,
                $this->logger
            );
        } elseif ($type === "json") {
            return new JsonImporter(
                $this->file,
                $this->serializer,
                $this->logger
            );
        } else {
            throw new \Magento\Framework\Exception\LocalizedException(
                __("Unsupported Profile type specified, valid Profile types - csv or json")
            );
        }
    }
}
