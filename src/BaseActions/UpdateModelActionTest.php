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

    protected function setUp(): void
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
        $model = factory($this->resourceModelFullClassName)->create($this->originalAttributes);
        $attributes = factory($this->resourceModelFullClassName, $this->factoryName)->raw($this->dirtyAttributes);

        $response = $this->callRoute($model->getKey(), $attributes);

        $this->assertDatabaseHas($model->getTable(), $attributes);
        $this->assertDatabaseMissing($model->getTable(), $model->getAttributes());

        $response
            ->assertStatus(200)
            ->assertExactJson(['data' => $model->fresh($this->withRelationships)->toArray()]);
    }
}
