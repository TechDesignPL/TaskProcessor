<?php

namespace TechDesign\TaskProcessor\Action;

use GuzzleHttp\Client;
use TechDesign\TaskProcessor\Action;
use TechDesign\TaskProcessor\Helper\Printer;

class InstallAssetsAction extends Action
{
    protected $configFile;
    protected $outputDir;

    public function __construct($configFile, $outputDir)
    {
        $this->configFile = $configFile;
        $this->outputDir = $outputDir;
    }

    public function copyDirectory($src, $dst)
    {
        $dir = opendir($src);
        @mkdir($dst);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->copyDirectory($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    public function moveDirectory($src, $dst)
    {
        $this->copyDirectory($src, $dst);
        $this->removeDirectory($src);
    }

    public function removeDirectory($dir)
    {
        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->removeDirectory("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }


    function moveUp($dir)
    {
        // If the destination directory does not exist create it
        if (!is_dir($dir)) {
            if (!mkdir($dir)) {
                // If the destination directory could not be created stop processing
                return;
            }
        }

        $upDir = preg_replace('~[\\|/]([^\\|/]+)$~', '', $dir);

        $this->moveDirectory($dir, $upDir);
    }

    public function downloadAndExtract($url, $dir)
    {
        if (!is_dir($dir)) {
            mkdir($dir);
        } else {
            $this->removeDirectory($dir);
        }

        $ext = substr($url, strrpos($url, '.') + 1, strlen($url));

        $temp = tempnam(getcwd(), $ext);
        $client = new Client(['verify' => false]);
        $client->request('GET', $url, ['sink' => $temp]);

        switch ($ext) {
            case 'tgz':
                $phar = new \PharData($temp);
                $phar->extractTo($dir);
                //move files up
                if (is_dir($dir . '/package')) {
                    $this->moveUp($dir . '/package');
                }
                break;
            case 'zip':
                $zip = new \ZipArchive();
                if (($err = $zip->open($temp)) === true) {
                    $zip->extractTo($dir);
                    $zip->close();

                    $childDir = current(glob($dir . '/*', GLOB_ONLYDIR));
                    if (is_dir($childDir)) {
                        $this->moveUp($childDir);
                    }
                } else {
                    throw new \Exception('Could not read zip archive: ' . $err);
                }
                break;
            default:
                throw new \Exception('Unrecognized Archive');
        }
        unlink($temp);
    }

    public function run($input)
    {
        $config = json_decode(file_get_contents($this->configFile));
        foreach ($config->require as $package => $version) {
            $dir = rtrim($this->outputDir, '\\/') . '/' . $package;

            Printer::prnt(sprintf("Downloading and extracting '%s' archive", $package), Printer::FG_LIGHT_GREEN);

            if (strpos($version, 'http') === 0) {
                //is locked to specific commit hash?
                if (strpos($version, '#') !== false) {
                    list($url, $hash) = explode('#', $version);
                    $url = rtrim($url, '\\/');
                } else {
                    $url = rtrim($version, '\\/');
                    $hash = 'master';
                }

                $this->downloadAndExtract($url . '/archive/' . $hash . '.zip', $dir);
            } else {
                $result = json_decode(file_get_contents('https://registry.npmjs.org/' . $package . '/' . $version));
                if (!empty($result)) {
                    $this->downloadAndExtract($result->dist->tarball, $dir);
                }
            }
        }

        return $input;
    }
}