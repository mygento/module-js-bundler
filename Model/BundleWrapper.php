<?php

/**
 * @author Mygento Team
 * @copyright 2019-2026 Mygento (https://www.mygento.com)
 * @package Mygento_JsBundler
 */

namespace Mygento\JsBundler\Model;

class BundleWrapper
{
    private const MODULE_WRAPPER = "(function (define) {\n%s\n}).call(this, defineAs(%s));";
    private const TEXT_RESOURCE_WRAPPER = "define(%s, function () {\n    return %s;\n});";
    private const BUNDLES_CONFIG_WRAPPER = "require.config(%s);\n";
    private const BUNDLE_WRAPPER = <<<'JS'
(function (define) {
    var defineAs = function (id) {
        var namedDefine = function () {
            var args = Array.prototype.slice.call(arguments);

            if (typeof args[0] !== 'string') {
                args.unshift(id);
            }

            return define.apply(this, args);
        };

        namedDefine.amd = define.amd;

        return namedDefine;
    };
%s
}).call(this, define);

JS;

    public function wrapModule(string $id, string $source): string
    {
        return sprintf(self::MODULE_WRAPPER, $source, json_encode($id, JSON_UNESCAPED_SLASHES));
    }

    public function wrapTextResource(string $id, string $text): string
    {
        return sprintf(
            self::TEXT_RESOURCE_WRAPPER,
            json_encode($id, JSON_UNESCAPED_SLASHES),
            json_encode($text, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
        );
    }

    public function wrapBundle(array $wrappedModules): string
    {
        return sprintf(self::BUNDLE_WRAPPER, implode("\n", $wrappedModules));
    }

    public function wrapBundlesConfig(array $bundledModules): string
    {
        return sprintf(
            self::BUNDLES_CONFIG_WRAPPER,
            json_encode(['bundles' => $bundledModules], JSON_UNESCAPED_SLASHES),
        );
    }
}
