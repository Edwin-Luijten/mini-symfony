<?php

namespace Installer\Handlers;

final class ParametersHandler extends AbstractHandler implements HandlerInterface
{
    public function execute()
    {
        $this->write('');
        $this->writeHeader('Setting your parameters <info>(because you will forget it)</info>.');

        $this->writeGenerating('secret');
        $this->replaceParameter('secret', bin2hex(random_bytes(45)));

        $this->writeGenerating('encryption_key');
        $this->replaceParameter('encryption_key', bin2hex(random_bytes(45)));
    }
}