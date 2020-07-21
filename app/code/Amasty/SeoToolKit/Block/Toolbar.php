<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_SeoToolKit
 */


namespace Amasty\SeoToolKit\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Zend\Uri\UriFactory;

class Toolbar extends Template
{
    const OPTIMAL_TITLE_LENGTH = 60;
    const OPTIMAL_DESC_LENGTH = 155;
    const OPTIMAL_KEY_LENGTH = 160;
    const WARNING_CLASS = '-warning';
    const NORMAL_CLASS = '-normal';

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    /**
     * @var array
     */
    private $images = [];

    /**
     * @var string
     */
    private $title = '';

    /**
     * @var string
     */
    private $keywords = '';

    /**
     * @var string
     */
    private $metaDescription = '';

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    public function __construct(
        Context $context,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->setTemplate('Amasty_SeoToolKit::toolbar.phtml');
        parent::__construct($context, $data);
        $this->request = $request;
        $this->registry = $registry;
    }

    /**
     * @return string
     */
    public function getPageHtml()
    {
        return $this->getData('html');
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    public function getPageState()
    {
        $result = '';
        $name = $this->request->getModuleName();

        switch ($name) {
            case 'cms':
                $result = __('CMS Page');
                break;
            case 'catalog':
                $controller = $this->request->getControllerName();

                if ($controller == 'product') {
                    $result = __('Product View Page');
                } elseif ($controller == 'category') {
                    $result = __('Category View Page');

                    if (strpos($this->getPageHtml(), 'page-with-filter') !== false) {
                        $result .= __(' (with Navigation)');
                    }
                }
        }

        return $result;
    }

    /**
     * @param string $html
     */
    private function collectImages($html)
    {
        if (preg_match_all('/<img\s+[^>]*src="([^"]*)"[^>]*>/', $html, $result)) {
            if (isset($result[0]) && isset($result[1])) {
                foreach ($result[0] as $key => $image) {
                    if (strpos($image, 'alt') === false
                        || (strpos($image, 'alt=""') !== false
                            || strpos($image, "alt=''") !== false)
                    ) {
                        if (isset($result[1][$key])) {
                            $this->images[] = $result[1][$key];
                        }
                    }
                }
            }
        }
    }

    /**
     * @return array
     */
    public function getImagesWithoutAlt()
    {
        $this->collectImages($this->getPageHtml());

        return $this->images;
    }

    /**
     * @return string
     */
    public function getRobotsMeta()
    {
        $html = $this->getPageHtml();
        $meta = '';

        if (preg_match_all('/<meta\s+name="robots"\s+content="(.*?)".*>/', $html, $result)) {
            if (isset($result[0]) && isset($result[1])) {
                $meta = implode(',', $result[1]);
            }
        }

        return $meta;
    }

    /**
     * @return array
     */
    public function getCanonicalText()
    {
        $html = $this->getPageHtml();
        $text = __('There is no canonical tag set for this page');
        $status = self::WARNING_CLASS;

        if (preg_match_all('/<link\s+rel="canonical"\s+href="(.*?)".*>/s', $html, $result)) {
            if (isset($result[0]) && isset($result[1])) {
                $text = __('A canonical tag is set for this page');
                $status = self::NORMAL_CLASS;
            }
        }

        return [$text, $status];
    }

    /**
     * @return array
     */
    public function getTitleCount()
    {
        $html = $this->getPageHtml();
        $status = self::WARNING_CLASS;
        $text = __('%1 characters — not good.', 0);

        if (preg_match_all('/<title>(.*?)<\/title>/s', $html, $result)) {
            if (isset($result[0]) && isset($result[1][0])) {
                $this->title = $result[1][0];
                $count = $this->getTextLength($this->title);

                if ($count > self::OPTIMAL_TITLE_LENGTH) {
                    $text = __(
                        '%1 characters — Google typically displays the first 50–60 characters of a title tag. '
                        . 'If you keep your titles under 60 characters, our research suggests that you can expect'
                        . ' about 90% of your titles to display properly.',
                        $count
                    );
                } elseif ($count) {
                    $text = __('%1 characters — optimal.', $count);
                    $status = self::NORMAL_CLASS;
                }
            }
        }

        return [$text, $status];
    }

    /**
     * @return array
     */
    public function getMetaDescCount()
    {
        $html = $this->getPageHtml();
        $status = self::WARNING_CLASS;
        $text = __('%1 characters — not good.', 0);

        if (preg_match_all('/<meta\s+name="description"\s+content="(.*?)".*>/s', $html, $result)) {
            if (isset($result[0]) && isset($result[1][0])) {
                $this->metaDescription = $result[1][0];
                $count = $this->getTextLength($this->metaDescription);
                $text = __('%1 characters — optimal.', $count);
                $status = self::NORMAL_CLASS;
            }
        }

        return [$text, $status];
    }

    /**
     * @return array
     */
    public function getKeywordCount()
    {
        $html = $this->getPageHtml();
        $status = self::WARNING_CLASS;
        $text = __('%1 characters — not good.', 0);

        if (preg_match_all('/<meta\s+name="keywords"\s+content="(.*?)".*>/s', $html, $result)) {
            if (isset($result[0]) && isset($result[1][0])) {
                $this->keywords = $result[1][0];
                $count = $this->getTextLength($this->keywords);

                if ($count > self::OPTIMAL_KEY_LENGTH) {
                    $text = __(
                        '%1 characters —  When writing meta descriptions, we tend to keep it between 150-160 '
                        . 'characters because Google usually truncate snippets that are longer than 160 characters.',
                        $count
                    );
                } elseif ($count) {
                    $text = __('%1 characters — optimal.', $count);
                }

                $status = self::NORMAL_CLASS;
            }
        }

        return [$text, $status];
    }

    /**
     * @return array
     */
    public function getHeadings()
    {
        $html = $this->getPageHtml();
        $headings = [];
        $status = self::WARNING_CLASS;
        $searchElements = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'];

        foreach ($searchElements as $searchElement) {
            $find = [];
            $search = '/<' . $searchElement . '.*?>(.*?)<\/' . $searchElement .'>/s';

            if (preg_match_all($search, $html, $result)) {
                if (isset($result[0]) && isset($result[1][0])) {
                    $find = $result[1];
                }
            }

            $headings[$searchElement] = $find;
            $status = self::NORMAL_CLASS;
        }

        return [$headings, $status];
    }

    /**
     * @return array
     */
    public function getIframeText()
    {
        $html = $this->getPageHtml();
        $text = __('No IFrames found on this page');

        if (preg_match('/<iframe(.*?)\/iframe>/s', $html, $result)) {
            if (isset($result[0]) && isset($result[1])) {
                $text = __('The page contains IFrames');
            }
        }

        return $text;
    }

    /**
     * @return bool
     */
    public function isSchemaOrgExists()
    {
        $html = $this->getPageHtml();

        return strpos($html, 'schema.org') !== false;
    }

    /**
     * @return array
     */
    public function getLinksData()
    {
        $html = $this->getPageHtml();
        $types = [
            'nofollow' => [
                'label' => __('NoFollow Links'),
                'count' => 0
            ],
            'dofollow' => [
                'label' => __('DoFollow Links'),
                'count' => 0
            ],
            'internal' => [
                'label' => __('Internal Links'),
                'count' => 0
            ],
            'external' => [
                'label' => __('External Links'),
                'count' => 0
            ]
        ];

        if (preg_match_all('/<a\s+href=["\']?([^"\'>]+)["\']?.*?>/s', $html, $result)) {
            if (isset($result[0]) && isset($result[1])) {
                $links = $result[0];
                $allLinks = count($links);

                $noFollow = array_filter($links, function ($value) {
                    return strpos($value, 'rel="nofollow"') !== false
                        || strpos($value, "rel='nofollow'") !== false;
                });

                $noFollow = count($noFollow);
                $types['nofollow']['count'] = $noFollow;
                $types['dofollow']['count'] = $allLinks - $noFollow;

                $external = array_filter($result[1], function ($url) {
                    try {
                        $uri = UriFactory::factory($url);
                        $host = $uri->getHost();
                    } catch (\Zend\Uri\Exception\InvalidArgumentException $exc) {
                        $host = null;
                    }

                    return isset($host) && $host !== $this->request->getHttpHost();
                });

                $external = count($external);
                $types['external']['count'] = $external;
                $types['internal']['count'] = $allLinks - $external;
            }
        }

        return $types;
    }

    /**
     * array
     */
    public function getMetaTagTemplateData()
    {
        return $this->registry->registry('ammeta_applied_rule');
    }

    /**
     * @return string
     */
    public function getTitleText()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getMetaDescText()
    {
        return $this->metaDescription;
    }

    /**
     * @return string
     */
    public function getKeywordsText()
    {
        return $this->keywords;
    }

    /**
     * @return int
     */
    public function getMaxTitleLength()
    {
        return self::OPTIMAL_TITLE_LENGTH;
    }

    /**
     * @return int
     */
    public function getMetaDescLength()
    {
        return self::OPTIMAL_DESC_LENGTH;
    }

    /**
     * @return int
     */
    public function getMetaKeyLength()
    {
        return self::OPTIMAL_KEY_LENGTH;
    }

    /**
     * @param $text
     *
     * @return int
     */
    protected function getTextLength($text)
    {
        // phpcs:ignore
        $length = mb_strlen(html_entity_decode($text, ENT_QUOTES));

        return $length;
    }
}
