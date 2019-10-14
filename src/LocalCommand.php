<?php

namespace OsBre\ComposerLocalPackages;

use Composer\Command\BaseCommand;
use Composer\Factory;
use Composer\Installer;
use Composer\Json\JsonFile;
use Symfony\Component\Console\Input\{InputInterface, InputArgument, InputOption};
use Symfony\Component\Console\Output\OutputInterface;

class LocalCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('local')
            ->setDefinition(array(
                new InputArgument('package-path', InputArgument::REQUIRED),
                new InputOption('no-dev', null, InputOption::VALUE_NONE, 'Disables installation of require-dev packages.'),
                new InputOption('optimize-autoloader', 'o', InputOption::VALUE_NONE, 'Optimize autoloader during autoloader dump'),
            ));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = $this->getIO();
        $composer = $this->getComposer();
        $projectFile = new JsonFile(Factory::getComposerFile());

        $dirPath = getcwd() . "/" . $input->getArgument('package-path');

        if (!is_dir($dirPath)){
            return $io->writeError("<error> Path '$dirPath' does not exits.</error>");
        }

        $filePath = $dirPath."/composer.json";

        if (!file_exists($filePath)){
            return $io->writeError("<error> There is no composer.json, in the required directory.</error>");
        }

        $packageName = (new JsonFile($filePath))->read()['name'];

        $this->addRepository([
            'type' => 'path',
            'version' => 'dev-master',
            'url' => realpath($dirPath)
        ], $projectFile);

        $this->addDependency($packageName, 'dev-master', $projectFile);

        $optimize = $input->getOption('optimize-autoloader') || $composer->getConfig()->get('optimize-autoloader');
        return Installer::create($io, $this->getComposer())
            ->setDevMode(!$input->getOption('no-dev'))
            ->setOptimizeAutoloader($optimize)
            ->run();
    }

    private function addRepository(array $item, JsonFile $file)
    {
        $json = $file->read();

        if (!array_key_exists('repositories', $json)) {
            foreach ($json as $property => $value) {
                $newFile[$property] = $value;

                if ($property === "require") {
                    $newFile['repositories'] = [$item];
                }
            }
            return $file->write($newFile);
        }

        $alreadyExits = false;

        foreach ($json['repositories'] as $repository) {
            if ($repository === $item) {
                $alreadyExits = true;
            }
        }

        if (!$alreadyExits) {
            $json['repositories'][] = $item;
        }

        return $file->write($json);
    }

    private function addDependency(string $name, string $version, JsonFile $file): void
    {
        $json = $file->read();

        if (!array_key_exists($name, $json['require'])){
            $json['require'][$name] = $version;
            $file->write($json);
        }
    }
}