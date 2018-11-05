<?php

namespace Dannerz\LaravelApiTestingSuite\Traits;

trait Searches
{
    protected $searchStringColumns = [];

    protected $searchEnumColumns = [];

    /** @test */
    function searches_string_columns()
    {
        foreach ($this->searchStringColumns as $stringColumn) {

            $models = factory($this->resourceModelFullClassName, 15)->persist([$stringColumn => 'Foo']);
            factory($this->resourceModelFullClassName, 10)->create([$stringColumn => 'Faa']);

            $queryString = '?search=Foo';

            $response = $this->callRoute($queryString);

            $response
                ->assertStatus(200)
                ->assertJsonCount(15, 'data');

            $this->assertEquals(
                $models->toArray(),
                array_values(array_sort($response->json('data'), $this->resourceModel->getKeyName()))
            );

            $this->emptyResourceModelTable();
        }
    }

    /** @test */
    function searches_enum_columns()
    {
        foreach ($this->searchEnumColumns as $enumColumn) {

            // TODO: Issue here when $enumColumnValue1 & $enumColumnValue2 have overlap.

            $enumColumnValue1 = factory($this->resourceModelFullClassName)->make()->$enumColumn;
            $enumColumnValue2 = factory_diff($this->resourceModelFullClassName, $enumColumn, $enumColumnValue1);

            $models = factory($this->resourceModelFullClassName, 15)->persist([$enumColumn => $enumColumnValue1]);
            factory($this->resourceModelFullClassName, 10)->create([$enumColumn => $enumColumnValue2]);

            $queryString = '?search='.$enumColumnValue1;

            $response = $this->callRoute($queryString);

            $response
                ->assertStatus(200)
                ->assertJsonCount(15, 'data');

            $this->assertEquals(
                $models->toArray(),
                array_values(array_sort($response->json('data'), $this->resourceModel->getKeyName()))
            );

            $this->emptyResourceModelTable();
        }
    }
}
