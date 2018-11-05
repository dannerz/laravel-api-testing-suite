<?php

namespace Dannerz\LaravelApiTestingSuite\Traits;

use Illuminate\Database\Eloquent\Collection;

trait SortsNumberColumns
{
    protected $sortNumberColumns = [
        // number_column => isNullable : boolean
    ];

    /** @test */
    function sorts_number_columns()
    {
        foreach ($this->sortNumberColumns as $numberColumn => $isNullable) {

            $model6 = factory($this->resourceModelFullClassName)->create([$numberColumn => 116]);
            $model2 = factory($this->resourceModelFullClassName)->create([$numberColumn => 112]);
            $model1 = factory($this->resourceModelFullClassName)->create([$numberColumn => $isNullable ? null : 111]);
            $model8 = factory($this->resourceModelFullClassName)->create([$numberColumn => 118]);
            $model3 = factory($this->resourceModelFullClassName)->create([$numberColumn => 113]);
            $model7 = factory($this->resourceModelFullClassName)->create([$numberColumn => 117]);
            $model4 = factory($this->resourceModelFullClassName)->create([$numberColumn => 114]);
            $model5 = factory($this->resourceModelFullClassName)->create([$numberColumn => 115]);

            // Asc.
            $queryString = '?sort='.$numberColumn;
            $response = $this->callRoute($queryString);
            $response->assertStatus(200);
            $this->assertEquals(
                (new Collection([$model1, $model2, $model3, $model4, $model5, $model6, $model7, $model8]))->fresh()->toArray(),
                $response->json('data')
            );

            // Desc.
            $queryString = '?sort=-'.$numberColumn;
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
