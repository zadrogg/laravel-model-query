<?php

namespace Traits\Filters;

trait Filter
{
    private $query;

    public function setFilterArray(string $field, array $params = null)
    {
        if (is_array($params) && $params != null) {
            $this->query = $this->query->whereIn($field, $params);
        }

        return $this;
    }

    public function setFilters(array $filters = null)
    {
        if ($filters != null && is_array($filters)) {

            foreach ($filters as $filter) {

                $filter = (object) $filter;

                if (property_exists($filter, 'additional')) {
                    continue;
                }

                if ($filter->type == 'list') {

                    $this->query = $this->query->whereIn($filter->field, $filter->value);
                } elseif ($filter->type == 'string') {

                    $this->query = $this->query->where($filter->field, 'ilike', '%' . $filter->value . '%');
                } elseif ($filter->type == 'datetime') {

                    $this->query = $this->query->where(function ($query) use ($filter) {
                        $query->when(property_exists($filter->value, 'from'), function ($query) use ($filter) {
                            $query->where($filter->field, '>=', $filter->value->from);
                        });
                        $query->when(property_exists($filter->value, 'to'), function ($query) use ($filter) {
                            $query->where($filter->field, '<=', $filter->value->to . ' 23:59:59');
                        });
                    });
                } elseif ($filter->type == 'range') {

                    $this->query = $this->query->where(function ($query) use ($filter) {
                        $query->when(property_exists($filter->value, 'from'), function ($query) use ($filter) {
                            $query->where($filter->field, '>=', $filter->value->from);
                        });
                        $query->when(property_exists($filter->value, 'to'), function ($query) use ($filter) {
                            $query->where($filter->field, '<=', $filter->value->to);
                        });
                    });
                } elseif ($filter->type == 'time') {

                    $this->query = $this->query->where(function ($query) use ($filter) {
                        $query->when(property_exists($filter->value, 'from'), function ($query) use ($filter) {
                            $query->where($filter->field, '>=', $filter->value->from);
                        });
                        $query->when(property_exists($filter->value, 'to'), function ($query) use ($filter) {
                            $query->where($filter->field, '<=', $filter->value->to);
                        });
                    });
                } else {
                    $this->query = $this->query->where($filter->field, $filter->value);
                }
            }
        }

        return $this;
    }

    public function setSearch(string $field = null, string $value = null)
    {
        if ($field != null && $value != null) {

            $this->query = $this->query->where($field, 'ilike', '%' . $value . '%');
        }

        return $this;
    }

    public function setMultipleSearch(string $searchString = null, array $search = null)
    {
        if ($searchString !== null) {

            if (is_array($search)) {

                $this->query = $this->query->where(function ($query) use ($searchString, $search) {

                    foreach ($search as $searchColumn) {

                        $query->orWhere($searchColumn, 'ilike', '%' . $searchString . '%');
                    }
                });
            }
        }

        return $this;
    }

    public function setSearchWithModel(
        string $searchString = null,
        string $additional_model = null,
        string $additional_field = null) {
        if ($searchString !== null && $additional_model !== null) {

            $this->query = $this->query->orWhereHas($additional_model, function ($query) use ($additional_field, $searchString) {
                $query->where($additional_field, 'ilike', '%' . $searchString . '%');
            });
        };

        return $this;
    }

    public function setOrWhereExists(
        string $table,
        string $field,
        string $join_table,
        string $join_foreign,
        string $join_key,
        array $conditions = null,
        string $value = null,
        ...$select) {
        if ($value !== null) {

            $this->query = $this->query->orWhereExists(function ($query) use (
                $select,
                $table,
                $join_table,
                $join_foreign,
                $join_key,
                $conditions,
                $field,
                $value
                )
            {
                $query->select(...$select)
                    ->from($table)
                    ->join($join_table, $join_foreign, '=', $join_key)
                    ->when($conditions, function ($query) use ($conditions) {

                        foreach ($conditions as $condition) {

                            $query->whereRaw($condition);
                        }
                    })
                    ->where($field, 'ilike', '%' . $value . '%');
            });
        }

        return $this;
    }
}
