<?php

namespace Dannerz\LaravelApiTestingSuite\Traits\Sort;

use Illuminate\Database\Eloquent\Collection;

trait SortsDateColumns
{
    protected $sortDateColumns = [
        // date_column => isNullable : boolean
    ];

    /** @test */
    function sorts_date_columns()
    {
        foreach ($this->sortDateColumns as $dateColumn => $isNullable) {

            $model6 = factory($this->resourceModelFullClassName)->create([$dateColumn => '2016-10-11']);
            $model2 = factory($this->resourceModelFullClassName)->create([$dateColumn => '2016-09-09']);
            $model1 = factory($this->resourceModelFullClassName)->create([$dateColumn => $isNullable ? null : '2016-08-09']);
            $model8 = factory($this->resourceModelFullClassName)->create([$dateColumn => '2016-11-12']);
            $model3 = factory($this->resourceModelFullClassName)->create([$dateColumn => '2016-09-11']);
            $model7 = factory($this->resourceModelFullClassName)->create([$dateColumn => '2016-11-11']);
            $model4 = factory($this->resourceModelFullClassName)->create([$dateColumn => '2016-09-12']);
            $model5 = factory($this->resourceModelFullClassName)->create([$dateColumn => '2016-09-14']);

            // Asc.
            $queryString = '?sort='.$dateColumn;
            $response = $this->callRoute($queryString);
            $response->assertStatus(200);
            $this->assertEquals(
                (new Collection([$model1, $model2, $model3, $model4, $model5, $model6, $model7, $model8]))->fresh()->toArray(),
                $response->json('data')
            );

            // Desc.
            $queryString = '?sort=-'.$dateColumn;
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
