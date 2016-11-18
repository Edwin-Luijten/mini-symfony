<?php

namespace Installer\Handlers;

use Installer\Helpers\File;

final class CleanerHandler extends AbstractHandler implements HandlerInterface
{
    public function execute()
    {
        $this->write('');
        $this->writeHeader('Cleaning up <info>(also something you will forget)</info>.');

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

        File::remove(File::INSTALLER);

        $this->write('You are ready to rock!');
    }
}