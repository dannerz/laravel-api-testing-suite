<?php

namespace Dannerz\LaravelApiTestingSuite\BaseActions;

use Dannerz\LaravelApiTestingSuite\ActionTest;

abstract class FindModelActionTest extends ActionTest
{
    protected $method = 'GET';

    protected function setUp()
    {
        $childTestClassName = array_last(explode('\\', get_class(new static())));
        $resourceModelClassName = str_replace(['Find', 'ActionTest'], '', $childTestClassName);

        $this->resource = snake_case(str_plural($resourceModelClassName));
        $this->resourceModelClassName = str_singular($resourceModelClassName);

        parent::setUp();
    }

    /** @test */
    function finds_and_returns_data()
    {
        $model = factory($this->resourceModelFullClassName)->create();
        factory($this->resourceModelFullClassName, 15)->create();

        $response = $this->callRoute($model->getKey());

        $response
            ->assertStatus(200)
            ->assertExactJson(['data' => $model->fresh()->toArray()]);
    }
}
