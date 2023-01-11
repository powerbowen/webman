<?php

declare(strict_types=1);

namespace communal\tool;

use DateTimeInterface;
use JsonSerializable;
use SimpleXMLElement;
use stdClass;

final class Json
{
    /**
     * @param mixed $data
     * @return bool|string
     */
    public static function encode(mixed $data): bool|string
    {
        return json_encode(self::processData($data), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param string $json
     * @return mixed
     */
    public static function decode(string $json): mixed
    {
        return $json ? json_decode($json, true, 512, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : null;
    }

    /**
     * @param mixed $data
     * @return mixed
     */
    private static function processData(mixed $data): mixed
    {
        if (is_object($data)) {
            if ($data instanceof JsonSerializable) {
                return self::processData($data->jsonSerialize());
            }

            if ($data instanceof DateTimeInterface) {
                return self::processData((array)$data);
            }

            if ($data instanceof SimpleXMLElement) {
                $data = (array)$data;
            } else {
                $result = [];
                foreach ($data as $name => $value) {
                    $result[$name] = $value;
                }
                $data = $result;
            }

            if ($data === []) {
                return new stdClass();
            }
        }

        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if (is_array($value) || is_object($value)) {
                    $data[$key] = self::processData($value);
                }
            }
        }

        return $data;
    }
}