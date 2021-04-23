<?php

namespace Manadev\LayeredNavigation\Plugins;

use Magento\Framework\App\State;
use Magento\Framework\View\Asset\ConfigInterface;
use Magento\Framework\View\Design\Fallback\RulePool;
use Magento\Framework\View\Design\FileResolution\Fallback\ResolverInterface;
use Magento\Framework\View\Design\ThemeInterface;
use Magento\Framework\View\Template\Html\MinifierInterface;

class TemplateFilePlugin
{
    /**
     * @var ResolverInterface
     */
    protected $resolver;
    /**
     * @var MinifierInterface
     */
    protected $templateMinifier;
    /**
     * @var State
     */
    protected $appState;
    /**
     * @var ConfigInterface
     */
    protected $assetConfig;

    public function __construct(ResolverInterface $resolver,
        MinifierInterface $templateMinifier, State $appState,
        ConfigInterface $assetConfig)
    {
        $this->resolver = $resolver;
        $this->templateMinifier = $templateMinifier;
        $this->appState = $appState;
        $this->assetConfig = $assetConfig;
    }

    public function aroundGetFile($subject, callable $proceed,
        $area, ThemeInterface $themeModel, $file, $module = null)
    {
        if ($module !== 'Manadev_LayeredNavigation') {
            return $proceed($area, $themeModel, $file, $module);
        }

        if ($file !== 'navigation.phtml') {
            return $proceed($area, $themeModel, $file, $module);
        }

        if (!$this->assetConfig->isMinifyHtml()) {
            return $proceed($area, $themeModel, $file, $module);
        }

        if ($this->appState->getMode() !== State::MODE_PRODUCTION) {
            return $proceed($area, $themeModel, $file, $module);
        }

        $fallbackType = RulePool::TYPE_TEMPLATE_FILE;

        $template = $this->resolver->resolve($fallbackType,
            $file, $area, $themeModel, null, $module);

        return $this->templateMinifier->getMinified($template);
    }
}
