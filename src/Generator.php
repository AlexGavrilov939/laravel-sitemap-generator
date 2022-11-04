<?php

namespace Sitemap;


use SimpleXMLElement;
use Sitemap\Exceptions\FileCreatingException;
use Sitemap\Exceptions\UnexceptedFileTypeException;
use Sitemap\Exceptions\UnexceptedResultException;

/**
 * Sitemap generator class.
 */
class Generator
{
    const SITEMAP_FILE_TYPE__XML = 'xml';
    const SITEMAP_FILE_TYPE__CSV = 'csv';
    const SITEMAP_FILE_TYPE__JSON = 'json';

    /**
     * @param array $data
     * @param string $sitemapFileType
     * @param string $sitemapFilePath
     * @param string $sitemapFilename
     * @return string
     * @throws FileCreatingException
     * @throws UnexceptedFileTypeException
     * @throws UnexceptedResultException
     */
    public function generateSitemap(array $data, string $sitemapFileType, string $sitemapFilePath, string $sitemapFilename): string
    {
        if ($sitemapFileType === self::SITEMAP_FILE_TYPE__XML) {
            $content = $this->generateXmlFromArray($data);
        } elseif ($sitemapFileType === self::SITEMAP_FILE_TYPE__JSON) {
            $content = $this->generateJsonFromArray($data);
        } elseif ($sitemapFileType === self::SITEMAP_FILE_TYPE__CSV) {
            $content = $this->generateCsvFromArray($data, ';');
        } else {
            throw new UnexceptedFileTypeException();
        }

        if (! file_exists($sitemapFilePath)) {
            mkdir($sitemapFilePath, 0777, true);
        }

        $sitemapFile = "{$sitemapFilePath}/{$sitemapFilename}";
        $result = file_put_contents($sitemapFile, $content);
        if (! $result) {
            throw new FileCreatingException('Unable to create sitemap file', 100503);
        }

        return $sitemapFile;
    }

    /**
     * @param array $data
     * @return bool|string
     * @throws UnexceptedResultException
     */
    private function generateXmlFromArray(array $data): bool|string
    {
        $xml = new SimpleXMLElement("<?xml version='1.0' encoding='UTF-8' ?><urlset xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance' xmlns='http://www.sitemaps.org/schemas/sitemap/0.9' xsi:schemaLocation='http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd' />");
        $this->multidimensionalArrayToXml($xml, $data, 'url');

        $xmlStr = $xml->asXML();
        if (! $xmlStr) {
            throw new UnexceptedResultException('Unexpected error occurred. Check your file type and try again.', 100501);
        }

        return $xmlStr;
    }

    /**
     * @param array $data
     * @param string $delimiter
     * @return string
     */
    private function generateCsvFromArray(array $data, string $delimiter=':'): string
    {
        $csvStr = '';
        $titles = array_keys($data[0] ?? []);
        $csvStr .= implode($delimiter, $titles).PHP_EOL;
        foreach ($data as $item) {
            $csvStr .= implode($delimiter, $item).PHP_EOL;
        }

        return $csvStr;
    }

    /**
     * @throws UnexceptedResultException
     */
    private function generateJsonFromArray(array $data): string
    {
        $jsonStr = json_encode($data, JSON_PRETTY_PRINT);
        if (! $jsonStr) {
            throw new UnexceptedResultException('Unexpected error occurred. Check your file type and try again.', 100502);
        }

        return $jsonStr;
    }

    /**
     * @param SimpleXMLElement $xmlObj
     * @param array $data
     * @param string $nodeKey
     * @return void
     */
    private function multidimensionalArrayToXml(SimpleXMLElement $xmlObj, array $data, string $nodeKey = 'item'): void
    {
        foreach ($data as $key => $value) {
            if (is_numeric($key)) {
                $key = $nodeKey;
            }
            if (is_array($value)) {
                $node = $xmlObj->addChild($key);
                $this->multidimensionalArrayToXml($node, $value);
            } else {
                $xmlObj->addChild($key, htmlspecialchars($value));
            }
        }
    }
}
