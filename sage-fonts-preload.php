<?php

use Illuminate\Support\Str;
use function Roots\asset;

if (! function_exists('get_theme_support') && get_theme_support( 'sage') !== true) {
	return;
}

add_filter('wp_head', function () {
	$is_bud = asset('manifest.json')->exists() ? true : false; // Check if Sage 10 is using Bud or Laravel Mix

	echo collect(
		json_decode(asset(($is_bud ? 'manifest.json' : 'mix-manifest.json'))->contents())
	)->keys()->filter(function ($item) {
		return Str::endsWith($item, ['.otf', '.eot', '.woff', '.woff2', '.ttf']);
	})->map(function ($item) use($is_bud) {
		$asset_uri = $is_bud ? asset($item)->uri() : substr(asset($item)->uri(), 0, strpos(asset($item)->uri(), '?id='));

		// Return asset uri without versioning query string
		return sprintf(
			'<link rel="preload" href="%s" as="font" crossorigin>',
			$asset_uri
		);
	})->implode("\n");
});

