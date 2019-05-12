<?php

namespace Dannerz\LaravelApiTestingSuite\Traits\Sort;

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

            $model6 = factory($this->resourceModelFullClassName)->create([$numberColumn => 6]);
            $model2 = factory($this->resourceModelFullClassName)->create([$numberColumn => 2]);
            $model1 = factory($this->resourceModelFullClassName)->create([$numberColumn => $isNullable ? null : 1]);
            $model8 = factory($this->resourceModelFullClassName)->create([$numberColumn => 8]);
            $model3 = factory($this->resourceModelFullClassName)->create([$numberColumn => 3]);
            $model7 = factory($this->resourceModelFullClassName)->create([$numberColumn => 7]);
            $model4 = factory($this->resourceModelFullClassName)->create([$numberColumn => 4]);
            $model5 = factory($this->resourceModelFullClassName)->create([$numberColumn => 5]);

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
