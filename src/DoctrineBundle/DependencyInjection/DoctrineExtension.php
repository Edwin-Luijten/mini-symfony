<?php

namespace DoctrineBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Loader\DirectoryLoader;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

class DoctrineExtension extends Extension
{
    /**
     * @var string
     */
    private $defaultConnection;

    public function getAlias()
    {
        return 'doctrine';
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = $this->getConfiguration($configs, $container);

        $config = $this->processConfiguration($configuration, $configs);

        $fileLocator = new FileLocator(__DIR__ . '/../Resources/config');
        $loader = new DirectoryLoader($container, $fileLocator);
        $loader->setResolver(new LoaderResolver(array(
            new XmlFileLoader($container, $fileLocator),
            $loader,
        )));
        $loader->load('services/');

        $this->configureDbal($config['dbal'], $container);

        $this->configureClassesToCompile();
    }

    private function configureDbal($config, ContainerBuilder $container) {

        if (empty($config['default_connection'])) {
            $keys = array_keys($config['connections']);
            $config['default_connection'] = reset($keys);
        }

        $this->defaultConnection = $config['default_connection'];

        $container->setAlias('database_connection', sprintf('doctrine.dbal.%s_connection', $this->defaultConnection));
        $container->setAlias('doctrine.dbal.event_manager', new Alias(sprintf('doctrine.dbal.%s_connection.event_manager', $this->defaultConnection), false));

        $container->setParameter('doctrine.dbal.connection_factory.types', $config['types']);

        $connections = array();

        foreach (array_keys($config['connections']) as $name) {
            $connections[$name] = sprintf('doctrine.dbal.%s_connection', $name);
        }

        $container->setParameter('doctrine.connections', $connections);
        $container->setParameter('doctrine.default_connection', $this->defaultConnection);

        $def = $container->getDefinition('doctrine.dbal.connection');
        if (method_exists($def, 'setFactory')) {
            // to be inlined in dbal.xml when dependency on Symfony DependencyInjection is bumped to 2.6
            $def->setFactory(array(new Reference('doctrine.dbal.connection_factory'), 'createConnection'));
        } else {
            // to be removed when dependency on Symfony DependencyInjection is bumped to 2.6
            $def->setFactoryService('doctrine.dbal.connection_factory');
            $def->setFactoryMethod('createConnection');
        }

        foreach ($config['connections'] as $name => $connection) {
            $this->loadDbalConnection($name, $connection, $container);
        }
    }

    /**
     * Loads a configured DBAL connection.
     *
     * @param string           $name       The name of the connection
     * @param array            $connection A dbal connection configuration.
     * @param ContainerBuilder $container  A ContainerBuilder instance
     */
    protected function loadDbalConnection($name, array $connection, ContainerBuilder $container)
    {
        // configuration
        $configuration = $container->setDefinition(sprintf('doctrine.dbal.%s_connection.configuration', $name), new DefinitionDecorator('doctrine.dbal.connection.configuration'));
        $logger = null;
        if ($connection['logging']) {
            $logger = new Reference('doctrine.dbal.logger');
        }
        unset ($connection['logging']);
        if ($connection['profiling']) {
            $profilingLoggerId = 'doctrine.dbal.logger.profiling.'.$name;
            $container->setDefinition($profilingLoggerId, new DefinitionDecorator('doctrine.dbal.logger.profiling'));
            $profilingLogger = new Reference($profilingLoggerId);
            $container->getDefinition('data_collector.doctrine')->addMethodCall('addLogger', array($name, $profilingLogger));

            if (null !== $logger) {
                $chainLogger = new DefinitionDecorator('doctrine.dbal.logger.chain');
                $chainLogger->addMethodCall('addLogger', array($profilingLogger));

                $loggerId = 'doctrine.dbal.logger.chain.'.$name;
                $container->setDefinition($loggerId, $chainLogger);
                $logger = new Reference($loggerId);
            } else {
                $logger = $profilingLogger;
            }
        }
        unset($connection['profiling']);

        if (isset($connection['auto_commit'])) {
            $configuration->addMethodCall('setAutoCommit', array($connection['auto_commit']));
        }

        unset($connection['auto_commit']);

        if (isset($connection['schema_filter']) && $connection['schema_filter']) {
            $configuration->addMethodCall('setFilterSchemaAssetsExpression', array($connection['schema_filter']));
        }

        unset($connection['schema_filter']);

        if ($logger) {
            $configuration->addMethodCall('setSQLLogger', array($logger));
        }

        // event manager
        $container->setDefinition(sprintf('doctrine.dbal.%s_connection.event_manager', $name), new DefinitionDecorator('doctrine.dbal.connection.event_manager'));

        // connection
        // PDO ignores the charset property before 5.3.6 so the init listener has to be used instead.
        if (isset($connection['charset']) && version_compare(PHP_VERSION, '5.3.6', '<')) {
            if ((isset($connection['driver']) && stripos($connection['driver'], 'mysql') !== false) ||
                (isset($connection['driver_class']) && stripos($connection['driver_class'], 'mysql') !== false)) {
                $mysqlSessionInit = new Definition('%doctrine.dbal.events.mysql_session_init.class%');
                $mysqlSessionInit->setArguments(array($connection['charset']));
                $mysqlSessionInit->setPublic(false);
                $mysqlSessionInit->addTag('doctrine.event_subscriber', array('connection' => $name));

                $container->setDefinition(
                    sprintf('doctrine.dbal.%s_connection.events.mysqlsessioninit', $name),
                    $mysqlSessionInit
                );
                unset($connection['charset']);
            }
        }

        $options = $this->getConnectionOptions($connection);

        $def = $container
            ->setDefinition(sprintf('doctrine.dbal.%s_connection', $name), new DefinitionDecorator('doctrine.dbal.connection'))
            ->setArguments(array(
                $options,
                new Reference(sprintf('doctrine.dbal.%s_connection.configuration', $name)),
                new Reference(sprintf('doctrine.dbal.%s_connection.event_manager', $name)),
                $connection['mapping_types'],
            ))
        ;

        if (!empty($connection['use_savepoints'])) {
            $def->addMethodCall('setNestTransactionsWithSavepoints', array($connection['use_savepoints']));
        }
    }

    protected function getConnectionOptions($connection)
    {
        $options = $connection;

        if (isset($options['platform_service'])) {
            $options['platform'] = new Reference($options['platform_service']);
            unset($options['platform_service']);
        }
        unset($options['mapping_types']);

        if (isset($options['shard_choser_service'])) {
            $options['shard_choser'] = new Reference($options['shard_choser_service']);
            unset($options['shard_choser_service']);
        }

        foreach (array(
                     'options' => 'driverOptions',
                     'driver_class' => 'driverClass',
                     'wrapper_class' => 'wrapperClass',
                     'keep_slave' => 'keepSlave',
                     'shard_choser' => 'shardChoser',
                     'server_version' => 'serverVersion',
                     'default_table_options' => 'defaultTableOptions',
                 ) as $old => $new) {
            if (isset($options[$old])) {
                $options[$new] = $options[$old];
                unset($options[$old]);
            }
        }

        if (!empty($options['slaves']) && !empty($options['shards'])) {
            throw new InvalidArgumentException('Sharding and master-slave connection cannot be used together');
        }

        if (!empty($options['slaves'])) {
            $nonRewrittenKeys = array(
                'driver' => true, 'driverOptions' => true, 'driverClass' => true,
                'wrapperClass' => true, 'keepSlave' => true, 'shardChoser' => true,
                'platform' => true, 'slaves' => true, 'master' => true, 'shards' => true,
                'serverVersion' => true,
                // included by safety but should have been unset already
                'logging' => true, 'profiling' => true, 'mapping_types' => true, 'platform_service' => true,
            );
            foreach ($options as $key => $value) {
                if (isset($nonRewrittenKeys[$key])) {
                    continue;
                }
                $options['master'][$key] = $value;
                unset($options[$key]);
            }
            if (empty($options['wrapperClass'])) {
                // Change the wrapper class only if the user does not already forced using a custom one.
                $options['wrapperClass'] = 'Doctrine\\DBAL\\Connections\\MasterSlaveConnection';
            }
        } else {
            unset($options['slaves']);
        }

        if (!empty($options['shards'])) {
            $nonRewrittenKeys = array(
                'driver' => true, 'driverOptions' => true, 'driverClass' => true,
                'wrapperClass' => true, 'keepSlave' => true, 'shardChoser' => true,
                'platform' => true, 'slaves' => true, 'global' => true, 'shards' => true,
                // included by safety but should have been unset already
                'logging' => true, 'profiling' => true, 'mapping_types' => true, 'platform_service' => true,
            );
            foreach ($options as $key => $value) {
                if (isset($nonRewrittenKeys[$key])) {
                    continue;
                }
                $options['global'][$key] = $value;
                unset($options[$key]);
            }
            if (empty($options['wrapperClass'])) {
                // Change the wrapper class only if the user does not already forced using a custom one.
                $options['wrapperClass'] = 'Doctrine\\DBAL\\Sharding\\PoolingShardConnection';
            }
        } else {
            unset($options['shards']);
        }

        return $options;
    }

    /**
     * Run `bin/what-classes-to-compile.sh` to have an idea of what to add here.
     */
    private function configureClassesToCompile()
    {

    }

    /**
     * {@inheritDoc}
     */
    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return new Configuration($container->getParameter('kernel.debug'));
    }
}
