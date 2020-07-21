<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Meta
 */

namespace Amasty\Meta\Plugin\Theme\Block\Html;

class Title
{

    /**
     * @var \Amasty\Meta\Helper\Data
     */
    private $data;

    /**
     * @var array
     */
    private $emoji;

    public function __construct(
        \Amasty\Meta\Helper\Data $data
    ) {
        $this->data = $data;
    }

    public function aroundGetPageHeading(
        $subject,
        \Closure $proceed
    ) {
        $title = $proceed($subject);

        $replacedHeading = $this->data->getReplaceData('h1_tag');

        if ($replacedHeading) {
            return $replacedHeading;
        }
        return $title;
    }

    /**
     * @param $subject
     * @param string $params
     * @return array
     */
    public function beforeEscapeHtml(
        $subject,
        $params
    ) {
        $pattern = '/&#?[a-z]*[0-9]*;/';
        $emoji = [];
        preg_match_all($pattern, $params, $emoji);
        if (array_key_exists(0, $emoji)) {
            $this->emoji = $emoji[0];
        }
        $params = preg_split($pattern, $params);

        return [$params];
    }

    public function afterEscapeHtml(
        $subject,
        $escaped
    ) {
        $result = '';
        foreach ($escaped as $key => $item) {
            $result .= $item;
            if (array_key_exists($key, $this->emoji)) {
                $result .= $this->emoji[$key];
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getEmoji()
    {
        return $this->emoji;
    }

    /**
     * @param array $emoji
     */
    public function setEmoji($emoji)
    {
        $this->emoji = $emoji;
    }
}
