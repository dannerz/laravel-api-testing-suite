<?php

namespace Dannerz\LaravelApiTestingSuite\Traits\Filter;

trait FiltersEnumColumns
{
    protected $filterEnumColumns = [
        // enum_column => values : array (if nullable, null goes into array)
    ];

    /** @test */
    function filters_enum_columns()
    {
        foreach ($this->filterEnumColumns as $enumColumn => $values) {

            $value1 = array_random($values);
            $value2 = array_random($values);

            // $values array must contain more than 1 item.
            while ($value2 == $value1) {
                $value2 = array_random($values);
            }

            $models1 = factory($this->resourceModelFullClassName, 3)->create([$enumColumn => $value1]);
            $models2 = factory($this->resourceModelFullClassName, 4)->create([$enumColumn => $value2]);

            $value1 = is_null($value1) ? 'null' : $value1;
            $value2 = is_null($value2) ? 'null' : $value2;

            $queryString = '?filter['.$enumColumn.']='.$value1;
            $response = $this->callRoute($queryString);
            $response->assertStatus(200)->assertJsonCount(3, 'data');
            $this->assertEquals($models1->fresh()->toArray(), $response->json('data'));

            $queryString = '?filter['.$enumColumn.']='.$value1.','.$value2;
            $response = $this->callRoute($queryString);
            $response->assertStatus(200)->assertJsonCount(7, 'data');
            $this->assertEquals($models1->merge($models2)->fresh()->toArray(), $response->json('data'));

            $this->emptyResourceModelTable();
        }
    }
}
