<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-cache-warmer
 * @version   1.2.3
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */


$registration = dirname(dirname(dirname(__DIR__))) . '/vendor/mirasvit/module-cache-warmer/src/CacheWarmer/registration.php';
if (file_exists($registration)) {
    # module was already installed via composer
    return;
}

if (isset($_SERVER) && isset($_SERVER['HTTP_USER_AGENT'])
    && strpos($_SERVER['HTTP_USER_AGENT'], \Mirasvit\CacheWarmer\Api\Service\WarmerServiceInterface::USER_AGENT) === false
) { // STATUS_USER_AGENT also checked
    $_SERVER['FPC_TIME'] = microtime(true);
}

if (isset($_SERVER) && isset($_SERVER['HTTP_USER_AGENT'])
    && $_SERVER['HTTP_USER_AGENT'] == \Mirasvit\CacheWarmer\Api\Service\WarmerServiceInterface::STATUS_USER_AGENT
) {
    header('HTTP/1.1 301 Moved Permanently (Mirasvit warmer check if page is cached)');
    echo '*';
    exit;
}

\Magento\Framework\Component\ComponentRegistrar::register(
    \Magento\Framework\Component\ComponentRegistrar::MODULE,
    'Mirasvit_CacheWarmer',
    __DIR__
);