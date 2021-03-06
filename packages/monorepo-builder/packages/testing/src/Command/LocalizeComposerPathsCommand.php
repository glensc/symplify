<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Testing\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\MonorepoBuilder\FileSystem\ComposerJsonProvider;
use Symplify\MonorepoBuilder\Testing\ComposerJsonRepositoriesUpdater;
use Symplify\MonorepoBuilder\Testing\ComposerJsonRequireUpdater;
use Symplify\MonorepoBuilder\Testing\ValueObject\Option;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\SmartFileSystem\FileSystemGuard;
use Symplify\SmartFileSystem\SmartFileInfo;

final class LocalizeComposerPathsCommand extends Command
{
    /**
     * @var ComposerJsonProvider
     */
    private $composerJsonProvider;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var ComposerJsonRequireUpdater
     */
    private $composerJsonRequireUpdater;

    /**
     * @var FileSystemGuard
     */
    private $fileSystemGuard;

    /**
     * @var ComposerJsonRepositoriesUpdater
     */
    private $composerJsonRepositoriesUpdater;

    public function __construct(
        ComposerJsonProvider $composerJsonProvider,
        SymfonyStyle $symfonyStyle,
        ComposerJsonRequireUpdater $composerJsonRequireUpdater,
        ComposerJsonRepositoriesUpdater $composerJsonRepositoriesUpdater,
        FileSystemGuard $fileSystemGuard
    ) {
        $this->composerJsonProvider = $composerJsonProvider;
        $this->symfonyStyle = $symfonyStyle;
        $this->composerJsonRequireUpdater = $composerJsonRequireUpdater;
        $this->fileSystemGuard = $fileSystemGuard;

        parent::__construct();

        $this->composerJsonRepositoriesUpdater = $composerJsonRepositoriesUpdater;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Set mutual package paths to local packages - use for pre-split package testing');
        $this->addArgument(Option::PACKAGE_COMPOSER_JSON, InputArgument::REQUIRED, 'Path to package "composer.json"');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $packageComposerJson = (string) $input->getArgument(Option::PACKAGE_COMPOSER_JSON);
        $this->fileSystemGuard->ensureFileExists($packageComposerJson, __METHOD__);

        $packageComposerJsonFileInfo = new SmartFileInfo($packageComposerJson);
        $rootFileInfo = $this->composerJsonProvider->getRootFileInfo();

        // 1. update "require" to "*" for all local packages
        $packagesFileInfos = $this->composerJsonProvider->getPackagesComposerFileInfos();
        foreach ($packagesFileInfos as $packageFileInfo) {
            $this->composerJsonRequireUpdater->processPackage($packageFileInfo);
        }

        // 2. update "repository" to "*" for current composer.json
        $this->composerJsonRepositoriesUpdater->processPackage($packageComposerJsonFileInfo, $rootFileInfo);

        $message = sprintf(
            'Package paths in "%s" have been updated',
            $packageComposerJsonFileInfo->getRelativeFilePathFromCwd()
        );
        $this->symfonyStyle->success($message);

        return ShellCode::SUCCESS;
    }
}
