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
    var defineAs = (function (shimConfig) {
        var pendingModule = null;
        var defineScript = function () {
            var shim;

            if (pendingModule === null || pendingModule.defined) {
                return;
            }

            shim = shimConfig[pendingModule.id] || {};
            define(pendingModule.id, shim.deps || [], shim.exportsFn);
        };
        var defineAs = function (id) {
            var shimDeps = (shimConfig[id] || {}).deps || [];
            var wrapped = {id: id, defined: false};
            var namedDefine = function () {
                var args = Array.prototype.slice.call(arguments);

                if (typeof args[0] !== 'string') {
                    args.unshift(id);
                }

                if (args[0] === id) {
                    wrapped.defined = true;
                }

                if (args[0] === id && Array.isArray(args[1])) {
                    args[1] = args[1].concat(shimDeps);
                }

                return define.apply(this, args);
            };

            namedDefine.amd = define.amd;
            defineScript();
            pendingModule = wrapped;

            return namedDefine;
        };

        defineAs.end = defineScript;

        return defineAs;
    }(require.s.contexts._.config.shim));
%s
    defineAs.end();
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
