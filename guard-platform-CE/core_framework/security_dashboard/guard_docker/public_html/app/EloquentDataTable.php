<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Builder;

class EloquentDataTable extends \Yajra\DataTables\EloquentDataTable
{
    public function count()
    {
        /** @var Builder $builder */
        $builder = $this->prepareCountQuery();
        $table   = $this->connection->raw('(' . $builder->toSql() . ') count_row_table');

        return $this->connection->table($table)
            ->setBindings($builder->getQuery()->getBindings())
            ->count();
    }
}