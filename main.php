<?php

require('vendor/autoload.php');

$dataDir = getenv('KBC_DATADIR') === false ? '/data/' : getenv('KBC_DATADIR');

$destination = $dataDir . '/out/tables/';
$configFile = $dataDir . "/config.json";

if (!file_exists($configFile)) {
    echo "Config file not found";
    exit(2);
}

try {
    $fs = new \Symfony\Component\Filesystem\Filesystem();
    $jsonDecode = new \Symfony\Component\Serializer\Encoder\JsonDecode(true);
    $jsonEncode = new \Symfony\Component\Serializer\Encoder\JsonEncode();

    $config = $jsonDecode->decode(
        file_get_contents($configFile),
        \Symfony\Component\Serializer\Encoder\JsonEncoder::FORMAT
    );

    $parameters = (new \Symfony\Component\Config\Definition\Processor())->processConfiguration(
        new \Keboola\Processor\EscapeCharacter\ConfigDefinition(),
        [isset($config["parameters"]) ? $config["parameters"] : []]
    );

    $finder = new \Symfony\Component\Finder\Finder();
    $finder->notName("*.manifest")->in($dataDir . "/in/tables")->depth(0);
    /** @var $file SplFileInfo */
    foreach ($finder as $file) {
        $columnsInManifest = false;

        $manifestFile = $file->getPathname() . ".manifest";
        if (!$fs->exists($manifestFile)) {
            throw new \Keboola\Processor\EscapeCharacter\Exception(
                'Table ' . $file->getBasename() . ' does not have a manifest file.'
            );
        }
        $manifest = $jsonDecode->decode(
            file_get_contents($manifestFile),
            \Symfony\Component\Serializer\Encoder\JsonEncoder::FORMAT
        );

        if (!isset($manifest['delimiter'])) {
            throw new \Keboola\Processor\EscapeCharacter\Exception(
                'Manifest file for table ' . $file->getBasename() . ' does not specify delimiter.'
            );
        }
        if (!isset($manifest['enclosure'])) {
            throw new \Keboola\Processor\EscapeCharacter\Exception(
                'Manifest file for table ' . $file->getBasename() . ' does not specify enclosure.'
            );
        }
        $fs->rename($manifestFile, $destination . $file->getFilename() . ".manifest");

        if (is_dir($file->getPathname())) {
            // sliced file
            $slicedFiles = new FilesystemIterator($file->getPathname(), FilesystemIterator::SKIP_DOTS);
            $slicedDestination = $destination . $file->getFilename() . '/';
            if (!$fs->exists($slicedDestination)) {
                $fs->mkdir($slicedDestination);
            }
            foreach ($slicedFiles as $slicedFile) {
                \Keboola\Processor\EscapeCharacter\processFile(
                    $slicedFile,
                    $slicedDestination,
                    $manifest["delimiter"],
                    $manifest["enclosure"],
                    $parameters["escaped_by"]
                );
            }
        } else {
            \Keboola\Processor\EscapeCharacter\processFile(
                $file,
                $destination,
                $manifest["delimiter"],
                $manifest["enclosure"],
                $parameters["escaped_by"]
            );
        }
    }
} catch (\Keboola\Processor\EscapeCharacter\Exception $e) {
    echo $e->getMessage();
    exit(1);
} catch (\Symfony\Component\Config\Definition\Exception\InvalidConfigurationException $e) {
    echo "Invalid configuration: " . $e->getMessage();
    exit(1);
} catch (\Keboola\Csv\InvalidArgumentException $e) {
    echo $e->getMessage();
    exit(1);
} catch (\Exception $e) {
    echo $e->getMessage();
    exit(2);
}
