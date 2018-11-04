<?php

namespace Dannerz\LaravelApiTestingSuite\Traits;

trait Filters
{
    use FiltersForeignKeyColumns,
        FiltersStringColumns,
        FiltersNumberColumns,
        FiltersDateColumns,
        FiltersEnumColumns;
}
