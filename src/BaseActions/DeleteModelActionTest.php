<?php

namespace Dannerz\LaravelApiTestingSuite\BaseActions;

use Dannerz\LaravelApiTestingSuite\ActionTest;

abstract class DeleteModelActionTest extends ActionTest
{
    protected $method = 'DELETE';

    protected $softDeletes = true;

    protected $attributes = [];

    protected $guardedByChildRelationships = [
        // relationship => httpErrorCode : int
    ];

    protected $guardedByPivotRelationships = [
        // relationship => httpErrorCode : int
    ];

    protected $cascadeChildRelationships = [
        // relationship => softDeletes : boolean
    ];

    protected function setUp()
    {
        $childTestClassName = array_last(explode('\\', get_class(new static())));
        $resourceModelClassName = str_replace(['Delete', 'ActionTest'], '', $childTestClassName);

        $this->resource = snake_case(str_plural($resourceModelClassName));
        $this->resourceModelClassName = str_singular($resourceModelClassName);

        parent::setUp();
    }

    /** @test */
    function deletes()
    {
        $model = factory($this->resourceModelFullClassName)->create($this->attributes);

        $response = $this->callRoute($model->getKey());

        if ($this->softDeletes) {
            $this->assertSoftDeleted($model->getTable(), [$model->getKeyName() => $model->getKey()]);
        } else {
            $this->assertDatabaseMissing($model->getTable(), [$model->getKeyName() => $model->getKey()]);
        }

        $response->assertStatus(200)->assertExactJson([]);
    }

    /** @test */
    function is_guarded_by_child_relationships()
    {
        foreach ($this->guardedByChildRelationships as $relationship => $httpErrorCode) {

            $query = $this->resourceModel->$relationship();

            $model = factory($this->resourceModelFullClassName)->create($this->attributes);
            $relatedModel = factory(get_class($query->getRelated()))->create([
                $query->getForeignKey() => $model->getKey(),
            ]);

            $response = $this->callRoute($model->getKey());

            $this->assertDatabaseHas($model->getTable(), $model->getAttributes());

            $response->assertStatus($httpErrorCode);

            $relatedModel->delete();
        }
    }

    /** @test */
    function is_guarded_by_pivot_relationships()
    {
        foreach ($this->guardedByPivotRelationships as $relationship => $httpErrorCode) {

            $query = $this->resourceModel->$relationship();

            $model = factory($this->resourceModelFullClassName)->create($this->attributes);
            $relatedModel = factory(get_class($query->getRelated()))->create();
            $model->$relationship()->attach($relatedModel);

            $response = $this->callRoute($model->getKey());

            $this->assertDatabaseHas($model->getTable(), $model->getAttributes());

            $response->assertStatus($httpErrorCode);

            $relatedModel->delete();
        }
    }

    /** @test */
    function cascades_child_relationships()
    {
        foreach ($this->cascadeChildRelationships as $relationship => $softDeletes) {

            $query = $this->resourceModel->$relationship();

            $model = factory($this->resourceModelFullClassName)->create($this->attributes);
            $relatedModels = factory(get_class($query->getRelated()), 2)->create([
                $query->getForeignKey() => $model->getKey(),
            ]);

            $response = $this->callRoute($model->getKey());

            $this->assertCount(0, $model->$relationship()->get());

            foreach ($relatedModels as $relatedModel) {
                if ($softDeletes) {
                    $this->assertSoftDeleted($relatedModel->getTable(), [$relatedModel->getKeyName() => $relatedModel->getKey()]);
                } else {
                    $this->assertDatabaseMissing($relatedModel->getTable(), [$relatedModel->getKeyName() => $relatedModel->getKey()]);
                }
            }

            $response->assertStatus(200)->assertExactJson([]);
        }
    }
}
