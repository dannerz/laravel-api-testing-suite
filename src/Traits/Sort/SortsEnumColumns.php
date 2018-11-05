<?php

namespace Dannerz\LaravelApiTestingSuite\Traits\Sort;

use Illuminate\Database\Eloquent\Collection;

trait SortsEnumColumns
{
    protected $sortEnumColumns = [
        // enum_column => values : array (if nullable, null goes into array)
    ];

    /** @test */
    function sorts_enum_columns()
    {
        // In order for this to work, the ENUM values within the DB must be ordered.

        foreach ($this->sortEnumColumns as $enumColumn => $values) {

            $value1 = array_random($values);
            $value2 = array_random($values);

            // $values array must contain more than 1 item.
            while ($value2 == $value1) {
                $value2 = array_random($values);
            }

            $values = [$value1, $value2];
            sort($values);

            $model2 = factory($this->resourceModelFullClassName)->create([$enumColumn => $values[1]]);
            $model1 = factory($this->resourceModelFullClassName)->create([$enumColumn => $values[0]]);

            // Asc.
            $queryString = '?sort='.$enumColumn;
            $response = $this->callRoute($queryString);
            $response->assertStatus(200);
            $this->assertEquals(
                (new Collection([$model1, $model2]))->fresh()->toArray(),
                $response->json('data')
            );

            // Desc.
            $queryString = '?sort=-'.$enumColumn;
            $response = $this->callRoute($queryString);
            $response->assertStatus(200);
            $this->assertEquals(
                (new Collection([$model2, $model1]))->fresh()->toArray(),
                $response->json('data')
            );

            $this->emptyResourceModelTable();
        }
    }
}
