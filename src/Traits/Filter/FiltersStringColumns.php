<?php

namespace Dannerz\LaravelApiTestingSuite\Traits\Filter;

trait FiltersStringColumns
{
    protected $filterStringColumns = [
        // string_column => isNullable : boolean
    ];

    /** @test */
    function filters_string_columns()
    {
        foreach ($this->filterStringColumns as $stringColumn => $isNullable) {

            $models1 = factory($this->resourceModelFullClassName, 1)->create([$stringColumn => 'Aaa']);
            $models2 = factory($this->resourceModelFullClassName, 2)->create([$stringColumn => 'Bbb']);
            $models3 = factory($this->resourceModelFullClassName, 3)->create([$stringColumn => 'Ccc']);
            $models4 = factory($this->resourceModelFullClassName, 4)->create([$stringColumn => $isNullable ? null : 'Ddd']);
            $models5 = factory($this->resourceModelFullClassName, 5)->create([$stringColumn => 'Eee']);

            $queryString = '?filter['.$stringColumn.']=Aaa,'.($isNullable ? 'null' : 'Ddd');

            $response = $this->callRoute($queryString);

            $response->assertStatus(200)->assertJsonCount(5, 'data');

            $this->assertEquals($models1->merge($models4)->fresh()->toArray(), $response->json('data'));

            $this->emptyResourceModelTable();
        }
    }
}
