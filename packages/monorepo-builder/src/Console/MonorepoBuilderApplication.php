<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Console;

use Jean85\PrettyVersions;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\MonorepoBuilder\Console\Command\BumpInterdependencyCommand;
use Symplify\MonorepoBuilder\Console\Command\ValidateCommand;
use Symplify\MonorepoBuilder\Merge\Command\MergeCommand;
use Symplify\MonorepoBuilder\Release\Command\ReleaseCommand;
use Symplify\MonorepoBuilder\Validator\SourcesPresenceValidator;
use Symplify\MonorepoBuilder\ValueObject\File;
use Symplify\MonorepoBuilder\ValueObject\Option;
use Symplify\SymplifyKernel\Console\AbstractSymplifyConsoleApplication;

final class MonorepoBuilderApplication extends AbstractSymplifyConsoleApplication
{
    /**
     * @var SourcesPresenceValidator
     */
    private $sourcesPresenceValidator;

    /**
     * @param Command[] $commands
     */
    public function __construct(array $commands, SourcesPresenceValidator $sourcesPresenceValidator)
    {
        $this->addCommands($commands);
        $this->sourcesPresenceValidator = $sourcesPresenceValidator;

        $version = PrettyVersions::getVersion('symplify/monorepo-builder');

        parent::__construct('Monorepo Builder', $version->getPrettyVersion());
    }

    protected function getDefaultInputDefinition(): InputDefinition
    {
        $inputDefinition = parent::getDefaultInputDefinition();

        $inputDefinition->addOption(new InputOption(
            Option::CONFIG,
            'c',
            InputOption::VALUE_REQUIRED,
            'Path to config file',
            File::CONFIG
        ));

        return $inputDefinition;
    }

    protected function doRunCommand(Command $command, InputInterface $input, OutputInterface $output): int
    {
        $this->validateSources($command);

        return $this->doRunCommandAndShowHelpOnArgumentError($command, $input, $output);
    }

    private function validateSources(Command $command): void
    {
        $commandClass = get_class($command);

        if (in_array($commandClass, [ValidateCommand::class, MergeCommand::class], true)) {
            $this->sourcesPresenceValidator->validatePackageComposerJsons();
        }

        if (in_array($commandClass, [BumpInterdependencyCommand::class, ReleaseCommand::class], true)) {
            $this->sourcesPresenceValidator->validateRootComposerJsonName();
        }
    }
}
