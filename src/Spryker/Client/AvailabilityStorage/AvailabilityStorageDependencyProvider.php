<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\AvailabilityStorage;

use Spryker\Client\AvailabilityStorage\Dependency\Client\AvailabilityStorageToStorageClientBridge;
use Spryker\Client\AvailabilityStorage\Dependency\Service\AvailabilityStorageToSynchronizationServiceBridge;
use Spryker\Client\Kernel\AbstractDependencyProvider;
use Spryker\Client\Kernel\Container;

/**
 * @method \Spryker\Client\AvailabilityStorage\AvailabilityStorageConfig getConfig()
 */
class AvailabilityStorageDependencyProvider extends AbstractDependencyProvider
{
    public const CLIENT_STORAGE = 'CLIENT_STORAGE';

    public const SERVICE_SYNCHRONIZATION = 'SERVICE_SYNCHRONIZATION';

    public const PLUGINS_POST_PRODUCT_VIEW_AVAILABILITY_EXPAND = 'PLUGINS_POST_PRODUCT_VIEW_AVAILABILITY_EXPAND';

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    public function provideServiceLayerDependencies(Container $container)
    {
        $container = $this->addStorageClient($container);
        $container = $this->addSynchronizationService($container);

        $container = $this->addPostProductViewAvailabilityStorageExpandPlugins($container);

        return $container;
    }

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    protected function addStorageClient(Container $container): Container
    {
        $container[self::CLIENT_STORAGE] = function (Container $container) {
            return new AvailabilityStorageToStorageClientBridge($container->getLocator()->storage()->client());
        };

        return $container;
    }

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    protected function addSynchronizationService(Container $container): Container
    {
        $container[self::SERVICE_SYNCHRONIZATION] = function (Container $container) {
            return new AvailabilityStorageToSynchronizationServiceBridge($container->getLocator()->synchronization()->service());
        };

        return $container;
    }

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    protected function addPostProductViewAvailabilityStorageExpandPlugins(Container $container): Container
    {
        $container->set(static::PLUGINS_POST_PRODUCT_VIEW_AVAILABILITY_EXPAND, function (Container $container) {
            return $this->getPostProductViewAvailabilityStorageExpandPlugins();
        });

        return $container;
    }

    /**
     * @return \Spryker\Client\AvailabilityStorageExtension\Dependency\Plugin\PostProductViewAvailabilityStorageExpandPluginInterface[]
     */
    public function getPostProductViewAvailabilityStorageExpandPlugins(): array
    {
        return [];
    }
}
