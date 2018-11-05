<?php

namespace Dannerz\LaravelApiTestingSuite\Traits;

trait Sorts
{
    use SortsStringColumns,
        SortsNumberColumns,
        SortsDateColumns,
        SortsEnumColumns;
}
