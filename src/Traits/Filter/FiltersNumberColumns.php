<?php

namespace Dannerz\LaravelApiTestingSuite\Traits\Filter;

trait FiltersNumberColumns
{
    protected $filterNumberColumns = [
        // number_column => isNullable : boolean
    ];

    /** @test */
    function filters_number_columns()
    {
        foreach ($this->filterNumberColumns as $numberColumn => $isNullable) {

            $models1 = factory($this->resourceModelFullClassName, 1)->create([$numberColumn => 1]);
            $models2 = factory($this->resourceModelFullClassName, 2)->create([$numberColumn => 2]);
            $models3 = factory($this->resourceModelFullClassName, 3)->create([$numberColumn => 3]);
            $models4 = factory($this->resourceModelFullClassName, 4)->create([$numberColumn => $isNullable ? null : 4]);
            $models5 = factory($this->resourceModelFullClassName, 5)->create([$numberColumn => 5]);

            $queryString = '?filter['.$numberColumn.']=1,'.($isNullable ? 'null' : '4');

            $response = $this->callRoute($queryString);

            $response->assertStatus(200)->assertJsonCount(5, 'data');

            $this->assertEquals($models1->merge($models4)->fresh()->toArray(), $response->json('data'));

            $this->emptyResourceModelTable();
        }
    }
}
