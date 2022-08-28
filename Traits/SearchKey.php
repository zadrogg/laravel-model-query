<?php

namespace Zadrog\LaravelModelQuery\Traits;

trait SearchKey
{
    public function search_key(string $searchKey, array $data): object
    {
        if (isset($data[$searchKey])) {
            return (object) $data[$searchKey];
        }

        foreach ($data as $param) {

            if (is_array($param) && (array) ($res = $this->search_key($searchKey, $param))) {
                return $res;
            }
        }

        return (object) [];
    }

    public function search_key_from_object(string $searchKey, array $data): object
    {
        if (isset($data[$searchKey])) {
            return (object) $data;
        }

        foreach ($data as $param) {

            if (is_object($param) && property_exists($param, $searchKey))
                return $param;

            if (is_array($param) && (array) ($res = $this->search_key_from_object($searchKey, $param))) {
                return $res;
            }
        }

        return (object) [];
    }
}
