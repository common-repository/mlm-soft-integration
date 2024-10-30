<?php

namespace MLMSoft\traits;

trait FrontendLoadingTrait
{
    /** @var string */
    protected $assetsPrefix;

    /** @var string */
    protected $stylesPrefix;

    /** @var string */
    protected $scriptsPrefix;

    /** @var array */
    protected $scriptParams;

    /**
     * @var string
     */
    protected $dir;

    private $scriptsCount = 0;
    private $stylesCount = 0;

    public function initFrontend()
    {
        $this->assetsPrefix = $this->getPrefix();
        $this->stylesPrefix = $this->assetsPrefix . 'style_';
        $this->scriptsPrefix = $this->assetsPrefix . 'script_';
        $this->dir = $this->getBaseDir();
    }

    protected abstract function getBaseDir();

    protected abstract function getPrefix();

    protected abstract function getPathToStyles();

    protected abstract function getPathToEntries();

    public function addScriptParams($name, $params)
    {
        $this->scriptParams[$name] = $params;
    }

    protected function setModuleScripts($module = true)
    {
        add_filter('script_loader_tag', [$this, 'addTypeAttribute'], 10, 3);
    }

    public function addTypeAttribute($tag, $handle, $src)
    {
        if (!str_starts_with($handle, $this->scriptsPrefix)) {
            return $tag;
        }
        $tag = '<script type="module" src="' . esc_url($src) . '"></script>';
        return $tag;
    }

    public function enqueue()
    {
        $this->registerAssets();
        $this->processEnqueue();
    }

    /**
     * @since 3.6.6
     */
    public function getStylesCount()
    {
        return $this->stylesCount;
    }

    /**
     * @since 3.6.6
     */
    public function getStylesPrefix()
    {
        return $this->stylesPrefix;
    }

    protected function processEnqueue()
    {
        for ($i = 0; $i < $this->scriptsCount; $i++) {
            wp_enqueue_script($this->scriptsPrefix . $i, false, false, false, true);
        }
        
        for ($i = 0; $i < $this->stylesCount; $i++) {
            wp_enqueue_style($this->stylesPrefix . $i);
        }
    }

    private function registerAssets()
    {
        $pluginDir = plugin_dir_path($this->dir . '../');
        $entryPath = trim($this->getPathToEntries(), '/');
        $files = scandir($pluginDir . $entryPath);
        $this->scriptsCount = 0;
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                $scriptHandle = $this->scriptsPrefix . $this->scriptsCount;
                wp_register_script($scriptHandle, plugin_dir_url($this->dir) . $entryPath . '/' . $file);
                if (!empty($this->scriptParams)) {
                    foreach ($this->scriptParams as $name => $param) {
                        wp_localize_script($scriptHandle, $name, $param);
                    }
                }
                $this->scriptsCount++;
            }
        }

        $stylesPath = trim($this->getPathToStyles(), '/');
        $files = scandir($pluginDir . $stylesPath);
        $this->stylesCount = 0;
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..' && str_ends_with($file, '.css')) {
                wp_register_style($this->stylesPrefix . $this->stylesCount, plugin_dir_url($this->dir) . $stylesPath . '/' . $file);
                $this->stylesCount++;
            }
        }
    }
}