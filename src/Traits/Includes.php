<?php

namespace Dannerz\LaravelApiTestingSuite\Traits;

trait Includes
{
    protected $includeParentRelationships = [];

    protected $includePivotRelationships = [];

    protected $includePivotRelationshipsSortedByStringColumn = [];

    // TODO: All tests should really be contained within a loop.

    /** @test */
    function includes_parent_relationships()
    {
        $models = factory($this->resourceModelFullClassName, 5)->persist();

        $snakeCaseParentRelationships = array_map(function ($parentRelationship) {
            return snake_case($parentRelationship);
        }, $this->includeParentRelationships);

        $queryString = '?include='.implode(',', $snakeCaseParentRelationships);

        $response = $this->callRoute($queryString);

        $modelsAsArray = $models->map(function ($model) {
            $modelAsArray = $model->toArray();
            foreach ($this->includeParentRelationships as $parentRelationship) {
                $relatedModel = $model->$parentRelationship;
                $modelAsArray = array_merge($modelAsArray, [
                    snake_case($parentRelationship) => $relatedModel ? $relatedModel->toArray() : null,
                ]);
            }
            return $modelAsArray;
        })->all();

        $response->assertStatus(200);

        $this->assertEquals($modelsAsArray, $response->json('data'));
    }

    /** @test */
    function includes_pivot_relationships()
    {
        $models = factory($this->resourceModelFullClassName, 5)->persist();

        $relatedModelsByPivotRelationship = [];

        foreach ($this->includePivotRelationships as $pivotRelationship) {
            $pivotModelFullClassName = get_class($this->resourceModel->$pivotRelationship()->getRelated());
            $relatedModels = factory($pivotModelFullClassName, 2)->persist();
            $relatedModelsByPivotRelationship[$pivotRelationship] = $relatedModels;
        }

        foreach ($models as $model) {
            foreach ($relatedModelsByPivotRelationship as $pivotRelationship => $relatedModels) {
                $model->$pivotRelationship()->attach($relatedModels);
            }
        }

        $snakeCasePivotRelationships = array_map(function ($pivotRelationship) {
            return snake_case($pivotRelationship);
        }, $this->includePivotRelationships);

        $queryString = '?include='.implode(',', $snakeCasePivotRelationships);

        $response = $this->callRoute($queryString);

        $modelsAsArray = $models->map(function ($model) use ($relatedModelsByPivotRelationship) {
            $modelAsArray = $model->toArray();
            foreach ($this->includePivotRelationships as $pivotRelationship) {
                $modelAsArray = array_merge($modelAsArray, [
                    snake_case($pivotRelationship) => $relatedModelsByPivotRelationship[$pivotRelationship]->toArray(),
                ]);
            }
            return $modelAsArray;
        })->all();

        $response->assertStatus(200);

        $this->assertEquals($modelsAsArray, $response->json('data'));
    }

    /** @test */
    function includes_pivot_relationships_sorted_by_string_column()
    {
        foreach ($this->includePivotRelationshipsSortedByStringColumn as $includeName => $config) {

            $pivotRelationship = $config['pivot_relationship'];
            $stringColumn = $config['string_column'];

            $models = factory($this->resourceModelFullClassName, 5)->persist();

            $pivotModelFullClassName = get_class($this->resourceModel->$pivotRelationship()->getRelated());

            $relatedModel3 = factory($pivotModelFullClassName)->create([$stringColumn => 'Acc']);
            $relatedModel4 = factory($pivotModelFullClassName)->create([$stringColumn => 'Add']);
            $relatedModel1 = factory($pivotModelFullClassName)->create([$stringColumn => 'Aaa']);
            $relatedModel2 = factory($pivotModelFullClassName)->create([$stringColumn => 'Abb']);

            foreach ($models as $model) {
                $model->$pivotRelationship()->attach([
                    $relatedModel3->getKey(),
                    $relatedModel4->getKey(),
                    $relatedModel1->getKey(),
                    $relatedModel2->getKey(),
                ]);
            }

            $queryString = '?include='.snake_case($includeName);

            $response = $this->callRoute($queryString);

            $modelsAsArray = $models->map(function ($model) use ($includeName, $relatedModel1, $relatedModel2, $relatedModel3, $relatedModel4) {
                $modelAsArray = $model->toArray();
                $modelAsArray = array_merge($modelAsArray, [
                    snake_case($includeName) => [
                        $relatedModel1->fresh()->toArray(),
                        $relatedModel2->fresh()->toArray(),
                        $relatedModel3->fresh()->toArray(),
                        $relatedModel4->fresh()->toArray(),
                    ],
                ]);
                return $modelAsArray;
            })->all();

            $response->assertStatus(200);

            $this->assertEquals($modelsAsArray, $response->json('data'));

            $this->emptyResourceModelTable();
        }
    }
}
