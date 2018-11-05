<?php

namespace Dannerz\LaravelApiTestingSuite\BaseActions;

use Dannerz\LaravelApiTestingSuite\ActionTest;

abstract class UpdateModelActionTest extends ActionTest
{
    protected $method = 'PUT';

    protected $factoryName = 'api-update';

    protected $originalAttributes = [];

    protected $dirtyAttributes = [];

    protected $withRelationships = [
        // relationship
    ];

    protected function setUp()
    {
        $childTestClassName = array_last(explode('\\', get_class(new static())));
        $resourceModelClassName = str_replace(['Update', 'ActionTest'], '', $childTestClassName);

        $this->resource = snake_case(str_plural($resourceModelClassName));
        $this->resourceModelClassName = str_singular($resourceModelClassName);

        parent::setUp();
    }

    /** @test */
    function updates_and_returns_data()
    {
        $model = factory($this->resourceModelFullClassName)->persist($this->originalAttributes);
        $attributes = factory($this->resourceModelFullClassName, $this->factoryName)->raw($this->dirtyAttributes);

        $response = $this->callRoute($model->getKey(), $attributes);

        $newAttributes = array_merge($model->getAttributes(), $attributes);

        $this->assertDatabaseHas($model->getTable(), $newAttributes);
        $this->assertDatabaseMissing($model->getTable(), $model->getAttributes());

        $response
            ->assertStatus(200)
            ->assertExactJson(['data' => $model->fresh($this->withRelationships)->toArray()]);
    }
}
