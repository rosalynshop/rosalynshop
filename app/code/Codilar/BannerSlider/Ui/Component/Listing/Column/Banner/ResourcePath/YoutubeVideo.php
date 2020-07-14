<?php
/**
 *
 * @package     magento2
 * @author      Jayanka Ghosh (joy)
 * @license     https://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * @link        https://www.codilar.com/
 */

namespace Codilar\BannerSlider\Ui\Component\Listing\Column\Banner\ResourcePath;


class YoutubeVideo implements ProcessorInterface
{

    const YOUTUBE_ICON = 'data:image/png;base64,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';

    /**
     * @param array $item
     * @return string
     */
    public function process(array $item): string
    {
        $youtubeIcon = self::YOUTUBE_ICON;
        return sprintf('<img style="width: 100px; height: auto;" src="%s" alt="%s" />', $youtubeIcon, 'YouTube');
    }
}