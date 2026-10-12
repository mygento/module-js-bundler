<?php

/**
 * @author Mygento Team
 * @copyright 2019-2022 Mygento (https://www.mygento.ru)
 * @package Mygento_JsBundler
 */

namespace Mygento\JsBundler\Plugin;

use Magento\Deploy\Package\BundleInterface;
use Magento\Deploy\Service\Bundle;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\View\Asset\Minification;
use Magento\Framework\View\Design\Theme\ThemeProvider;
use Mygento\JsBundler\Model\BundleWrapper;
use Mygento\JsBundler\Model\Config\Bundles;
use Psr\Log\LoggerInterface;

class Builder
{
    public const JSEXT = '.js';
    public const BUNDLES_CONFIG_FILE = BundleInterface::BUNDLE_JS_DIR . '/requirejs-bundles.js';
    private const TEMPLATE_EXT = '.html';
    private const TEXT_RESOURCE_PREFIX = 'text!';
    private const BUNDLE_SUFFIX = '-bundle';

    private WriteInterface $pubStaticDir;

    public function __construct(
        Filesystem $filesystem,
        private Minification $minification,
        private Bundles $bundles,
        private ThemeProvider $themeProvider,
        private LoggerInterface $logger,
        private BundleWrapper $bundleWrapper,
    ) {
        $this->pubStaticDir = $filesystem->getDirectoryWrite(DirectoryList::STATIC_VIEW);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param mixed $result
     */
    public function afterDeploy(Bundle $subject, $result, string $area, string $theme, string $locale)
    {
        $packageDir = $area . '/' . $theme . '/' . $locale;
        $themeBundles = $this->bundles->getByTheme($this->themeProvider->getThemeByFullPath($area . '/' . $theme));

        $bundledModules = [];
        foreach ($themeBundles as $bundleName => $modules) {
            $wrappedModules = $this->wrapModules($packageDir, $bundleName, $modules);
            if (count($wrappedModules) < 2) {
                continue;
            }

            $bundleId = BundleInterface::BUNDLE_JS_DIR . '/' . $bundleName . self::BUNDLE_SUFFIX;
            $this->pubStaticDir->writeFile(
                $this->minification->addMinifiedSign($packageDir . '/' . $bundleId . self::JSEXT),
                $this->bundleWrapper->wrapBundle($wrappedModules),
            );
            $bundledModules[$bundleId] = array_keys($wrappedModules);
        }

        if ($bundledModules !== []) {
            $this->pubStaticDir->writeFile(
                $this->minification->addMinifiedSign($packageDir . '/' . self::BUNDLES_CONFIG_FILE),
                $this->bundleWrapper->wrapBundlesConfig($bundledModules),
            );
        }

        return $result;
    }

    private function wrapModules(string $packageDir, string $bundleName, array $modules): array
    {
        $wrappedModules = [];
        foreach ($modules as $id => $path) {
            if (strpos($id, self::TEXT_RESOURCE_PREFIX) === 0) {
                $wrappedModules[$id] = $this->wrapTextResource($packageDir, $bundleName, $id, $path);
            } else {
                $wrappedModules[$id] = $this->wrapModule($packageDir, $bundleName, $id, $path);
            }
        }

        return array_filter($wrappedModules);
    }

    private function wrapModule(string $packageDir, string $bundleName, string $id, string $path): ?string
    {
        $file = $this->minification->addMinifiedSign($packageDir . '/' . $path . self::JSEXT);
        if (!$this->pubStaticDir->isFile($file)) {
            $this->logger->warning(
                'JS bundle module skipped: file not found',
                ['bundle' => $bundleName, 'module' => $id, 'file' => $file],
            );

            return null;
        }

        $template = $packageDir . '/' . $path . self::TEMPLATE_EXT;
        if ($this->pubStaticDir->isFile($template)) {
            $this->logger->warning(
                'JS bundle module skipped: its template URL would resolve to the bundle',
                ['bundle' => $bundleName, 'module' => $id, 'template' => $template],
            );

            return null;
        }

        return $this->bundleWrapper->wrapModule($id, $this->pubStaticDir->readFile($file));
    }

    private function wrapTextResource(string $packageDir, string $bundleName, string $id, string $path): ?string
    {
        $file = $packageDir . '/' . ($path === $id ? substr($id, strlen(self::TEXT_RESOURCE_PREFIX)) : $path);
        if (!$this->pubStaticDir->isFile($file)) {
            $this->logger->warning(
                'JS bundle text resource skipped: file not found',
                ['bundle' => $bundleName, 'module' => $id, 'file' => $file],
            );

            return null;
        }

        return $this->bundleWrapper->wrapTextResource($id, $this->pubStaticDir->readFile($file));
    }
}
