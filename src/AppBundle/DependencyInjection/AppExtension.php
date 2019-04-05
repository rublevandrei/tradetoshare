<?php
namespace AppBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Yaml\Yaml;
class AppExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {

       $configs = Yaml::parse(file_get_contents(__DIR__.'/../Resources/config/config.yml'));
        $configuration = new Configuration('social_media');

        $config = $this->processConfiguration($configuration, $configs);

        $resourceOwners = array();
        foreach ($config['resource_owners'] as $name => $options) {
            $resourceOwners[] = $name;
            $lowerName = strtolower($name);

            $definition = new Definition();
            $definition->setFactory(array(new Reference('social_media.service_factory'), 'createService'));
            $definition->setClass('%social_media.service.' . $lowerName . '.class%');
            $definition->addArgument($name);

       //     $container->setDefinition('social_media.service.' . $lowerName, $definition);

            foreach ($options as $key => $value) {
               $container->setParameter('social_media.resource_owners.' . $lowerName . '.' . $key, $value);
            }
        }
//        echo '<pre>'; print_r($resourceOwners); echo '</pre>';exit();die();
       $container->setParameter('social_media.resource_owners', $resourceOwners);

        //$container->setParameter('food_entities',     $config['food_entities']);
        //$container->setParameter('acme_social', $config['acme_social']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load('admin.yml');
    }

//    /**
//     * {@inheritdoc}
//     */
//    public function getAlias()
//    {
//        return 'social_media';
//    }
}