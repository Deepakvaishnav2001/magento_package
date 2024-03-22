<?php


namespace Sharika\CustomerImport\Console\Command;

use Exception;
use Magento\Framework\Console\Cli;
use Magento\Framework\Filesystem;
use Magento\Framework\App\State;
use Magento\Framework\App\Area;
use Magento\Store\Model\StoreManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Sharika\CustomerImport\Api\ImportInterface;
use Sharika\CustomerImport\Model\ProfileManagement;
use Sharika\CustomerImport\Model\Customer;

class ImportCustomers extends Command
{
    /**
     * @var object
     */
    protected $importer;

    /**
     * @var ProfileManagement
     */
    protected ProfileManagement $profileManagement;

    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $storeManager;

    /**
     * @var Customer
     */

    private Customer $customer;

    /**
     * @var Filesystem
     */
    private Filesystem $filesystem;

    /**
     * @var State
     */
    private State $state;

    /**
     * CustomerImport constructor.
     *
     * @param ProfileManagement $profileManagement
     * @param Customer $customer
     * @param StoreManagerInterface $storeManager
     * @param Filesystem $filesystem
     * @param State $state
     */
    public function __construct(
        ProfileManagement $profileManagement,
        Customer $customer,
        StoreManagerInterface $storeManager,
        Filesystem $filesystem,
        State $state
    ) {
        parent::__construct();

        $this->profileManagement = $profileManagement;
        $this->customer = $customer;
        $this->storeManager = $storeManager;
        $this->filesystem = $filesystem;
        $this->state = $state;
    }

    /**
     * @inheritdoc
     */
    protected function configure(): void
    {
        $this->setName("customer:import");
        $this->setDescription("Customer Import via CSV & JSON");
        $this->setDefinition([
            new InputArgument(ImportInterface::PROFILE_NAME, InputArgument::REQUIRED, "Profile name ex: sample-csv"),
            new InputArgument(ImportInterface::FILE_PATH, InputArgument::REQUIRED, "File Path ex: sample.csv")
        ]);
        parent::configure();
    }

    /**
     * Executes the current command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output):int
    {
        $profileType = $input->getArgument(ImportInterface::PROFILE_NAME);
        $filePath = $input->getArgument(ImportInterface::FILE_PATH);
        $output->writeln(sprintf("Profile type: %s", $profileType));
        $output->writeln(sprintf("File Path: %s", $filePath));

        try {
            $this->state->setAreaCode(Area::AREA_GLOBAL);

            if ($importData = $this->getImporterInstance($profileType)->getImportData($input)) {
                $storeId = $this->storeManager->getStore()->getId();
                $websiteId = $this->storeManager->getStore($storeId)->getWebsiteId();

                foreach ($importData as $data) {
                    $this->customer->createCustomer($data, $websiteId, $storeId);
                }

                $output->writeln(sprintf("Total of %s Customers are imported", count($importData)));
                return Cli::RETURN_SUCCESS;
            }

            return Cli::RETURN_FAILURE;
        } catch (Exception $e) {
            $msg = $e->getMessage();
            $output->writeln("<error>$msg</error>", OutputInterface::OUTPUT_NORMAL);
            return Cli::RETURN_FAILURE;
        }
    }

    /**
     * Returns the profile instance
     *
     * @param string $profileType
     * @return ImportInterface
     * @throws Exception
     */
    protected function getImporterInstance($profileType): ImportInterface
    {
        if (!($this->importer instanceof ImportInterface)) {
            $this->importer = $this->profileManagement->create($profileType);
        }
        return $this->importer;
    }
}
