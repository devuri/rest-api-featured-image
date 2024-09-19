let mix = require('laravel-mix')

mix.setPublicPath('build').version()

mix.options({ manifest: false })

// build the plugin files in trunk.
mix.copy('vendor', 'build/trunk/vendor')
mix.copy('src', 'build/trunk/src')
mix.copy('asset', 'build/trunk/asset')

mix.copy([
    'uninstall.php',
    'index.php',
    'LICENSE',
    'readme.txt',
    'api-featured-image.php',
], 'build/trunk/')
