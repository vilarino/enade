<?php
/**
 * Created by PhpStorm.
 * User: rafael
 * Date: 04/03/17
 * Time: 09:25
 */

namespace Provider;


use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\Yaml\Yaml;

class YamlConfigConnection implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        try {
            $config = Yaml::parse(file_get_contents(__DIR__ . "/../../config/parameters.yml"));

            $pimple['connection'] = $config;

        } catch (\Exception $exc) {
            var_dump($exc->getMessage());
            exit;
        }
    }
}