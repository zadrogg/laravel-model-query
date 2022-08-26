<?php

namespace Traits\Model;

trait Relations
{
    private $query;

    public function appendQuery()
    {
        $this->query = $this->newModelQuery()->select();

        return $this;
    }

    public function setHas($relation, $foreign, $value)
    {
        $this->query = $this->query->whereHas($relation, function ($query) use ($foreign, $value) {
            $query->when($foreign, function ($query) use ($foreign, $value) {

                if (!is_array($value)) {

                    $query->where($foreign, $value);

                } else {

                    $query->whereIn($foreign, $value);
                }
            });
        });

        return $this;
    }

    public function setOrHas($relation, $foreign, $value)
    {
        $this->query = $this->query->orWhereHas($relation, function ($query) use ($foreign, $value) {
            $query->when($foreign, function ($query) use ($foreign, $value) {
                $query->where($foreign, $value);
            });
        });

        return $this;
    }

    public function setDoubleHas(array $firstRelation, array $secondRelation) //TODO универсальный

    {
        if (is_array($firstRelation) && is_array($secondRelation)) {

            $first = [
                'relation' => $firstRelation[0],
                'foreign'  => $firstRelation[1] ?? '',
                'value'    => $firstRelation[2] ?? '',
            ];

            $second = [
                'relation' => $secondRelation[0],
                'foreign'  => $secondRelation[1] ?? '',
                'value'    => $secondRelation[2] ?? '',
            ];

            $this->query = $this->query->whereHas($first['relation'], function ($query) use ($first, $second) {
                $query->when($first['foreign'], function ($query) use ($first) {

                    if (is_array($first['value'])) {
                        $query->whereIn($first['foreign'], $first['value']);
                    } else
                        $query->where($first['foreign'], $first['value']);
                });
                $query->whereHas($second['relation'], function ($query) use ($second) {
                    $query->when($second['foreign'], function ($query) use ($second) {

                        if (is_array($second['value'])) {
                            $query->whereIn($second['foreign'], $second['value']);
                        } else
                            $query->where($second['foreign'], $second['value']);
                    });
                });
            });
        }

        return $this;
    }

    public function setDoesntHave(string $relation, $foreign = null, $value = null)
    {
        $this->query = $this->query->whereDoesntHave($relation, function ($query) use ($foreign, $value) {
            $query->when($foreign, function ($query) use ($foreign, $value) {
                $query->where($foreign, $value);
            });
        });

        return $this;
    }

    public function noTrashed()
    {
        $this->query->whereNull('deleted_at');

        return $this;
    }

    public function onlyTrashed()
    {
        $this->query->whereNotNull('deleted_at');

        return $this;
    }

    public function setLeftJoinSub($joinModel, $model, $foreign, $foreignJoin, $filter = null, ...$condition)
    {
        $this->query = $this->query->leftJoinSub($joinModel, $model, function ($join) use ($foreign, $foreignJoin, $filter, $condition) {
            $join->on($foreign, '=', $foreignJoin)
                ->when($condition, function ($query) use ($condition) {
                    $query->where(...$condition);
                })
                ->when($filter, function ($query) use ($filter) {
                    $query->when(property_exists($filter, 'from'), function ($query) use ($filter) {
                        $query->where($filter->field, '>=', $filter->from);
                    });
                    $query->when(property_exists($filter, 'to'), function ($query) use ($filter) {
                        $query->where($filter->field, '<=', $filter->to);
                    });
                });
        });

        return $this;
    }

    public function setJoinSub($joinModel, $model, $foreign, $foreignJoin, $filter = null)
    {
        $this->query = $this->query->joinSub($joinModel, $model, function ($join) use ($foreign, $foreignJoin, $filter) {
            $join->on($foreign, '=', $foreignJoin)->when($filter, function ($query) use ($filter) {
                $query->when(property_exists($filter, 'from'), function ($query) use ($filter) {
                    $query->where($filter->field, '>=', $filter->from);
                });
                $query->when(property_exists($filter, 'to'), function ($query) use ($filter) {
                    $query->where($filter->field, '<=', $filter->to);
                });
            });
        });

        return $this;
    }

    public function setSelectAfterJoin(...$fieldModel)
    {
        $this->query = $this->query->select(...$fieldModel);

        return $this;
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function closeQuery($field = null, $order = null, $per_page = null) // collection

    {
        return $this->query->orderBy($field ?? 'created_at', $order ?? 'desc')->paginate($per_page);
    }

    public function closeQueryGet($field = null, $order = null) // collection

    {
        return $this->query->orderBy($field ?? 'created_at', $order ?? 'desc')->get();
    }
}
