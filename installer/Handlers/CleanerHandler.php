<?php

namespace Installer\Handlers;

use Installer\Helpers\File;

final class CleanerHandler extends AbstractHandler implements HandlerInterface
{
    public function execute()
    {
        $this->write('');
        $this->writeHeader('Cleaning up <info>(also something you will forget)</info>.');

        File::remove(File::INSTALLER);

        if($this->askConfirmation('Do you want to remove the \'paragonie/random_compat\' package?'))
        {
            exec(
                escapeshellcmd(
                    sprintf(
                        'composer remove %s -d "%s" ',
                        escapeshellarg('paragonie/random_compat'),
                        escapeshellarg(__DIR__ . '/../../')
                    )
                )
            );
        }

        $this->write('You are ready to rock!');
    }
}