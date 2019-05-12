<?php

namespace Dannerz\LaravelApiTestingSuite\BaseActions;

use Dannerz\LaravelApiTestingSuite\ActionTest;
use Dannerz\LaravelApiTestingSuite\Traits\Filter\Filters;
use Dannerz\LaravelApiTestingSuite\Traits\Sort\Sorts;

abstract class GetModelsActionTest extends ActionTest
{
    use Filters,
        Sorts;

    protected $method = 'GET';

    protected function setUp(): void
    {
        $childTestClassName = array_last(explode('\\', get_class(new static())));
        $resourceModelClassName = str_replace(['Get', 'ActionTest'], '', $childTestClassName);

        $this->resource = snake_case(str_plural($resourceModelClassName));
        $this->resourceModelClassName = str_singular($resourceModelClassName);

        parent::setUp();
    }

    /** @test */
    function returns_all()
    {
        $models = factory($this->resourceModelFullClassName, 20)->create();

        $response = $this->callRoute();

        $response->assertStatus(200)->assertJsonCount(20, 'data');

        $this->assertEquals($models->fresh()->toArray(), $response->json('data'));
    }
}
