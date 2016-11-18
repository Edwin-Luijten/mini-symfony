<?php

namespace Installer\Helpers;

use Symfony\Component\Filesystem\Filesystem;

class File
{
    const PARAMETERS = 'app/config/parameters.yml';
    const PARAMETERS_DIST = 'app/config/parameters.yml.dist';
    const PARAMETERS_DEVELOPMENT = 'app/config/parameters_development.yml';
    const GITIGNORE = '.gitignore';
    const COMPOSER_JSON = 'composer.json';
    const INSTALLER = 'installer';

    /**
     * @param $file
     * @param array $ignore
     */
    public static function removeLinesFromFile($file, array $ignore)
    {
        $lines = [];
        foreach (file($file) as $i => $line) {
            if (!in_array($i, $ignore)) {
                $lines[] = $line;
            }
        }
        file_put_contents($file, implode('', $lines));
    }

    /**
     * @param $file
     * @param array $content
     */
    public static function removeLinesFromFileByContent($file, array $content)
    {
        $lines = [];
        foreach (file($file) as $i => $line) {
            if (!in_array($line, $content)) {
                $lines[] = $line;
            }
        }
        file_put_contents($file, implode('', $lines));
    }

    /**
     * @param string $file
     * @return string
     */
    public static function getFullPath($file)
    {
        return self::getPathRoot() . $file;
    }

    /**
     * @return string
     */
    public static function getPathRoot()
    {
        return realpath(__DIR__ . '/' . str_repeat('../', 2)) . '/';
    }

    /**
     * @param $search
     * @param $replace
     * @param $filename
     */
    public static function replaceInFile($search, $replace, $filename)
    {
        file_put_contents(File::getFullPath($filename), str_replace(
            $search,
            $replace,
            file_get_contents(File::getFullPath($filename))
        ));
    }

    /**
     * @param $name
     * @param $value
     * @param $file
     */
    public static function replaceParameterInFile($name, $value, $file)
    {
        $parameters = [];
        foreach (file(self::getFullPath($file)) as $line) {
            if (strpos($line, $name . ':') !== false) {
                $lineData    = explode(':', $line);
                $lineData[1] = sprintf(' %s', $value) . PHP_EOL;
                $line        = implode(':', $lineData);
            }
            $parameters[] = $line;
        }

        file_put_contents(self::getFullPath($file), implode('', $parameters));
    }


    /**
     * @param $source
     * @param $destination
     */
    public static function copy($source, $destination)
    {
        self::getFilesystem()->copy($source, $destination, true);
    }

    /**
     * @param $files
     */
    public static function remove($files)
    {
        self::getFilesystem()->remove($files);
    }

    /**
     * @return Filesystem
     */
    public static function getFilesystem()
    {
        return new Filesystem();
    }
}