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

        $this->write('You are ready to rock!');
    }
}