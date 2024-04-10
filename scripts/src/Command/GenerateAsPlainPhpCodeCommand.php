<?php

declare(strict_types=1);

namespace Knowolo\Command;

use Knowolo\Exception;
use Knowolo\Generator\PhpCodeGenerator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function Knowolo\isEmpty;

// the name of the command is what users type after "php bin/console"
#[AsCommand(name: 'know:generate-as-plain-php-code')]
class GenerateAsPlainPhpCodeCommand extends Command
{
    private PhpCodeGenerator $phpCodeGenerator;

    public function __construct(PhpCodeGenerator $phpCodeGenerator)
    {
        parent::__construct();

        $this->phpCodeGenerator = $phpCodeGenerator;
    }

    /**
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     */
    protected function configure(): void
    {
        $this
            // the command description shown when running "php bin/console list"
            ->setDescription('Generates a class for a given ontology.')

            // arguments
            ->addArgument(
                'url_or_local_path_to_rdf_file',
                InputArgument::REQUIRED,
                'URL or local path to related RDF file'
            )
        ;
    }

    /**
     * @throws \InvalidArgumentException
     * @throws \Knowolo\Exception if parameter url_or_local_path_to_rdf_file can not be casted to string
     * @throws \Knowolo\Exception if RDF file does not exist
     * @throws \Knowolo\Exception if RDF file has unknown format
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \quickRdfIo\RdfIoException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string|null */
        $urlOrLocalPathToRdfFile = $input->getArgument('url_or_local_path_to_rdf_file');

        if (isEmpty($urlOrLocalPathToRdfFile)) {
            throw new Exception('Argument url_or_local_path_to_rdf_file can not be empty');
        }

        /** @var non-empty-string */
        $urlOrLocalPathToRdfFile = (string) $urlOrLocalPathToRdfFile;

        $phpCode = $this->phpCodeGenerator->generateFileData($urlOrLocalPathToRdfFile);

        $output->writeln($phpCode);

        return Command::SUCCESS;
    }
}
