/**
 * Part of fusion project.
 *
 * @copyright  Copyright (C) 2018 Asikart.
 * @license    MIT
 */

const fusion = require('windwalker-fusion');

// The task `main`
fusion.task('main', function () {
  // Watch start
  //fusion.watch('src/scss/**/*.scss');
  // Watch end

  // Compile Start
  //fusion.sass('src/scss/**/*.scss', 'dist/app.css');
  // Compile end
});

// The task `install`
fusion.task('install', function () {
  const nodePath = 'node_modules';
  const destPath = 'asset';

  // Underscore
  fusion.copy(`${nodePath}/underscore/underscore.*`, `${destPath}/js/core/`);
  fusion.copy(`${nodePath}/underscore.string/dist/*`, `${destPath}/js/core/`);

  // Backbone
  fusion.copy(`${nodePath}/backbone/backbone.js`, `${destPath}/js/core/backbone.js`);
  fusion.copy(`${nodePath}/backbone/backbone-min.js`, `${destPath}/js/core/backbone.min.js`);

  // URL Polyfill
  fusion.copy(`${nodePath}/url-polyfill/*.js`, `${destPath}/js/polyfill/`);

  // Promise Polyfill
  fusion.copy(`${nodePath}/promise-polyfill/dist/polyfill.js`, `${destPath}/js/polyfill/promise.js`);
  fusion.copy(`${nodePath}/promise-polyfill/dist/polyfill.min.js`, `${destPath}/js/polyfill/promise.min.js`);
});

fusion.default(['main']);

/*
 * APIs
 *
 * Compile entry:
 * fusion.js(source, dest, options = {})
 * fusion.babel(source, dest, options = {})
 * fusion.ts(source, dest, options = {})
 * fusion.typeScript(source, dest, options = {})
 * fusion.css(source, dest, options = {})
 * fusion.less(source, dest, options = {})
 * fusion.sass(source, dest, options = {})
 * fusion.copy(source, dest, options = {})
 *
 * Live Reload:
 * fusion.livereload(source, dest, options = {})
 * fusion.reload(file)
 *
 * Gulp proxy:
 * fusion.src(source, options)
 * fusion.dest(path, options)
 * fusion.task(name, deps, fn)
 * fusion.watch(glob, opt, fn)
 *
 * Stream Helper:
 * fusion.through(handler) // Same as through2.obj()
 *
 * Config:
 * fusion.disableNotification()
 * fusion.enableNotification()
 */
