<?php echo '<?xml version="1.0" encoding="UTF-8"?>'."\n"; ?>
{{--
    XML sitemap, served at /sitemap.xml by SitemapController.

    The declaration above is echoed from a raw PHP tag rather than written out
    literally, because Blade would otherwise read the "<?xml" as an opening PHP
    tag and fall over.
--}}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach ($urls as $url)
    <url>
        <loc>{{ $url['loc'] }}</loc>
@isset($url['lastmod'])
        <lastmod>{{ $url['lastmod'] }}</lastmod>
@endisset
        <changefreq>{{ $url['changefreq'] }}</changefreq>
        <priority>{{ $url['priority'] }}</priority>
    </url>
@endforeach
</urlset>
