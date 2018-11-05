<?php

namespace Dannerz\LaravelApiTestingSuite\BaseActions;

use Dannerz\LaravelApiTestingSuite\ActionTest;

abstract class CreateModelActionTest extends ActionTest
{
    protected $method = 'POST';

    protected $factoryName = 'api-create';

    protected $attributes = [];

    protected function setUp()
    {
        $childTestClassName = array_last(explode('\\', get_class(new static())));
        $resourceModelClassName = str_replace(['Create', 'ActionTest'], '', $childTestClassName);

        $this->resource = snake_case(str_plural($resourceModelClassName));
        $this->resourceModelClassName = str_singular($resourceModelClassName);

        parent::setUp();
    }

    /** @test */
    function creates_and_returns_data()
    {
        $attributes = factory($this->resourceModelFullClassName, $this->factoryName)->raw($this->attributes);

        $response = $this->callRouteWithoutPath($attributes);

        $key = $response->json('data.'.$this->resourceModel->getKeyName());

        $this->assertDatabaseHas($this->resourceModel->getTable(), array_add(
            $attributes, $this->resourceModel->getKeyName(), $key
        ));

        $response
            ->assertStatus(200)
            ->assertExactJson(['data' => $this->resourceModel::find($key)->toArray()]);
    }
}
