<?php

declare(strict_types=1);

namespace Knowolo;

use Curl\Curl;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

/**
 * empty() is not enough to check if something is really empty.
 * This function takes care of the edge cases.
 *
 * @api
 * @see https://stackoverflow.com/questions/718986/checking-if-the-string-is-empty
 */
function isEmpty(bool|float|int|string|null $input): bool
{
    if (null === $input) {
        return true;
    } elseif (is_int($input)) {
        return false;
    } elseif (is_float($input)) {
        return false;
    } elseif (is_bool($input)) {
        return false;
    } else { // its a string
        $input = trim($input);
        $input = (string) preg_replace('/\s/', '', $input);

        return 0 == strlen($input);
    }
}

/**
 * Cache responses for a while to reduce server load.
 *
 * @param int $limitDownloadSizeTo If greater 0, the downloaded file size is capped at x MB.
 *
 * @throws \Psr\Cache\InvalidArgumentException
 */
function sendCachedRequest(string $url, int $limitDownloadSizeTo = 0): string
{
    $cache = new FilesystemAdapter('cached_request', 86400, __DIR__.'/../var');

    $key = (string) preg_replace('/[\W]/', '_', $url);

    // ask cache for entry
    // if there isn't one, run HTTP request and return response content
    return $cache->get($key, function () use ($limitDownloadSizeTo, $url): string {
        $curl = new Curl();
        $curl->setConnectTimeout(30);
        $curl->setMaximumRedirects(10);
        $curl->setOpt(CURLOPT_FOLLOWLOCATION, true); // follow redirects
        $curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
        $curl->setOpt(CURLOPT_SSL_VERIFYHOST, false);

        if (0 < $limitDownloadSizeTo) {
            // limit the size of downloaded file in case its file with GB of data
            $rangeEnd = 1024 * 60; // 60 MB
            $curl->setOpt(CURLOPT_HTTPHEADER, ['Range: bytes=0-'.$rangeEnd]);
        }

        $curl->get($url);

        // lazy approach: we dont care if link exists or not, just if it has parseable content
        return $curl->rawResponse;
    });
}
