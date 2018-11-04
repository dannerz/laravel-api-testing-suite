<?php

namespace Dannerz\LaravelApiTestingSuite\Traits;

trait FiltersDateColumns
{
    protected $filterDateColumns = [
        // date_column => isNullable : boolean
    ];

    /** @test */
    function filters_date_columns()
    {
        foreach ($this->filterDateColumns as $dateColumn => $isNullable) {

            $models1 = factory($this->resourceModelFullClassName, 1)->create([$dateColumn => '2016-04-03']);
            $models2 = factory($this->resourceModelFullClassName, 2)->create([$dateColumn => '2016-04-04']);
            $models3 = factory($this->resourceModelFullClassName, 3)->create([$dateColumn => '2016-04-05']);
            $models4 = factory($this->resourceModelFullClassName, 4)->create([$dateColumn => $isNullable ? null : '2016-04-06']);
            $models5 = factory($this->resourceModelFullClassName, 5)->create([$dateColumn => '2016-04-07']);

            $queryString = '?filter['.$dateColumn.']=2016-04-03,'.($isNullable ? 'null' : '2016-04-06');

            $response = $this->callRoute($queryString);

            $response->assertStatus(200)->assertJsonCount(5, 'data');

            $this->assertEquals($models1->merge($models4)->fresh()->toArray(), $response->json('data'));

            $this->emptyResourceModelTable();
        }
    }
}
