<?php

namespace Dannerz\LaravelApiTestingSuite\Traits\Sort;

trait Sorts
{
    use SortsStringColumns,
        SortsNumberColumns,
        SortsDateColumns,
        SortsEnumColumns;
}
