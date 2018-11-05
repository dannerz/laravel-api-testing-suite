<?php

namespace Dannerz\LaravelApiTestingSuite\Traits;

use Illuminate\Database\Eloquent\Collection;

trait SortsStringColumns
{
    protected $sortStringColumns = [
        // string_column => isNullable : boolean
    ];

    /** @test */
    function sorts_string_columns()
    {
        foreach ($this->sortStringColumns as $stringColumn => $isNullable) {

            $model6 = factory($this->resourceModelFullClassName)->create([$stringColumn => 'Aaf']);
            $model2 = factory($this->resourceModelFullClassName)->create([$stringColumn => 'Aab']);
            $model1 = factory($this->resourceModelFullClassName)->create([$stringColumn => $isNullable ? null : 'Aaa']);
            $model8 = factory($this->resourceModelFullClassName)->create([$stringColumn => 'Aah']);
            $model3 = factory($this->resourceModelFullClassName)->create([$stringColumn => 'Aac']);
            $model7 = factory($this->resourceModelFullClassName)->create([$stringColumn => 'Aag']);
            $model4 = factory($this->resourceModelFullClassName)->create([$stringColumn => 'Aad']);
            $model5 = factory($this->resourceModelFullClassName)->create([$stringColumn => 'Aae']);

            // Asc.
            $queryString = '?sort='.$stringColumn;
            $response = $this->callRoute($queryString);
            $response->assertStatus(200);
            $this->assertEquals(
                (new Collection([$model1, $model2, $model3, $model4, $model5, $model6, $model7, $model8]))->fresh()->toArray(),
                $response->json('data')
            );

            // Desc.
            $queryString = '?sort=-'.$stringColumn;
            $response = $this->callRoute($queryString);
            $response->assertStatus(200);
            $this->assertEquals(
                (new Collection([$model8, $model7, $model6, $model5, $model4, $model3, $model2, $model1]))->fresh()->toArray(),
                $response->json('data')
            );

            $this->emptyResourceModelTable();
        }
    }
}
