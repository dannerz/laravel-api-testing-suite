<?php

namespace Dannerz\LaravelApiTestingSuite\Traits\Filter;

trait Filters
{
    use FiltersForeignKeyColumns,
        FiltersStringColumns,
        FiltersNumberColumns,
        FiltersDateColumns,
        FiltersEnumColumns;
}
