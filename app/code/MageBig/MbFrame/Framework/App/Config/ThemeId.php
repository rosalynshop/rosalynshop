<?php
/**
 * Copyright © magebig.com - All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageBig\MbFrame\Framework\App\Config;


class ThemeId
{
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;

    /**
     * ThemeId constructor.
     *
     * @param \Magento\Framework\App\ResourceConnection $resource
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->_resource     = $resource;
    }

    public function getThemeId()
    {
        $uri = '';
        if (isset($_SERVER['REQUEST_URI'])) {
            $uri = $_SERVER['REQUEST_URI'];
        }
        $checkArea = strpos($uri, 'mbframe');
        $themeId   = '';
        if ($checkArea !== false) {
            $params = explode('/', $uri);
            for ($i = 0; $i < count($params); ++$i) {
                if ($params[$i] == 'theme_id') {
                    $themeId = ucwords($params[$i + 1]);
                    break;
                }
            }
        } else {
            $connection  = $this->_resource->getConnection();
            $theme_table = $this->_resource->getTableName('design_config_grid_flat');
            $themeId     = $connection->fetchOne("SELECT theme_theme_id  FROM " . $theme_table);
        }

        return $themeId;
    }
}