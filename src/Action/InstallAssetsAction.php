<?php

namespace TechDesign\TaskProcessor\Action;

use TechDesign\TaskProcessor\Action;

class InstallAssetsAction extends Action
{
    protected $configFile;
    protected $outputDir;

    public function __construct($configFile, $outputDir)
    {
        $this->configFile = $configFile;
        $this->outputDir = $outputDir;
    }

    public function downloadAndExtract($url, $dir)
    {
        $temp = tempnam(sys_get_temp_dir(), 'tgz');
        file_put_contents($temp, fopen($url, 'r'));

        $phar = new \PharData($temp);
        if (!is_dir($dir)) {
            mkdir($dir);
        }
        $phar->extractTo($dir);
        unlink($temp);
    }

    public function run($input)
    {
        $config = json_decode(file_get_contents($this->configFile));
        foreach ($config->require as $package => $version) {
            if (strpos($version, 'http') === 0) {

            } else {
                $result = json_decode(file_get_contents('https://registry.npmjs.org/' . $package . '/' . $version));
                if (!empty($result)) {
                    $this->downloadAndExtract($result->dist->tarball, rtrim($this->outputDir, '\\/') . '/' . $package);
                }
            }
        }

        return $input;
    }
}