# Sitemap generator

[![Packagist Downloads](https://img.shields.io/packagist/dm/alexgavrilov/laravel-sitemap-generator)](https://packagist.org/packages/alexgavrilov939/alexgavrilov/laravel-sitemap-generator)

Generates sitemap files in xml, csv, json formats.

## Getting Started

### Installation

Sitemap generator requires PHP >= 7.1.

```shell
composer require alexgavrilov/laravel-sitemap-generator
```

### Documentation

Full documentation can be found over on [fakerphp.github.io](https://fakerphp.github.io).

### Basic Usage

Use `Sitemap\Generator::generateSitemap(array $data, string $sitemapFileType, string $sitemapFilePath, string $sitemapFilePrefix = 'sitemap')` to generate and save sitemap in json, xml or csv format.

## License

Sitemap generator is released under the MIT License. See [`LICENSE`](LICENSE) for details.
