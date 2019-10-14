<?php

namespace OsBre\ComposerLocalPackages;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\{PluginInterface, Capable};

class Plugin implements PluginInterface, Capable
{
    public function activate(Composer $composer, IOInterface $io) {}

    public function getCapabilities()
    {
        return [
            'Composer\Plugin\Capability\CommandProvider' => 'OsBre\ComposerLocalPackages\CommandProvider'
        ];
    }
}