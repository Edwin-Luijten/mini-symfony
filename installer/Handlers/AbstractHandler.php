<?php

namespace Installer\Handlers;

use Composer\IO\IOInterface;
use Composer\Script\Event;
use Installer\Helpers\File;

abstract class AbstractHandler
{
    /**
     * @var Event
     */
    protected $event;

    /**
     * @var IOInterface
     */
    protected $io;

    /**
     * @param Event $event
     * @return $this
     */
    public function setEvent(Event $event)
    {
        $this->event = $event;
        $this->io    = $event->getIO();

        return $this;
    }

    /**
     * @param $message
     */
    protected function writeHeader($message)
    {
        $this->io->write($message);
        $this->io->write(str_repeat('=', strlen($message)));
        $this->io->write('');
    }

    /**
     * @param $message
     * @param bool $newline
     * @param $verbosity
     */
    protected function write($message, $newline = true, $verbosity = IOInterface::NORMAL)
    {
        $this->io->write($message, $newline, $verbosity);
    }


    /**
     * @param $name
     */
    protected function writeGenerating($name)
    {
        $this->write(sprintf('Generating <comment>%s</comment>', $name));
    }

    /**
     * @param $name
     * @param $value
     * @param string $file
     */
    protected function replaceParameter($name, $value, $file = File::PARAMETERS)
    {
        if (is_null($value)) {
            $value = 'null';
        }

        $this->write(sprintf(
            'Setting parameter <comment>%s</comment> to <comment>%s</comment> in <comment>%s</comment>',
            $name,
            $value,
            $file
        ));

        File::replaceParameterInFile($name, $value, $file);
    }
}