<?php

namespace Traits\Model;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

trait DBQuery
{
    private $query;

    public function setWrapSelect($builder, string $alias)
    {
        $this->query = DB::table($builder, $alias);

        return $this;
    }

    public function setSelectAfterWrap(...$select)
    {
        $this->query = $this->query->select(...$select);

        return $this;
    }
}
