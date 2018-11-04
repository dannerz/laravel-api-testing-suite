<?php

namespace Dannerz\LaravelApiTestingSuite\Traits;

trait FiltersDateColumns
{
    protected $filterDateColumns = [
        // date_column => isNullable : boolean
    ];

    protected $filterDateColumnsWithRange = [
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

    /** @test */
    function filters_date_columns_with_range()
    {
        foreach ($this->filterDateColumns as $dateColumn => $isNullable) {

            $models1 = factory($this->resourceModelFullClassName, 1)->create([$dateColumn => '2017-01-01']);
            $models2 = factory($this->resourceModelFullClassName, 2)->create([$dateColumn => '2017-02-11']);
            $models3 = factory($this->resourceModelFullClassName, 3)->create([$dateColumn => '2017-03-16']);
            $models4 = factory($this->resourceModelFullClassName, 4)->create([$dateColumn => $isNullable ? null : '2017-04-21']);
            $models5 = factory($this->resourceModelFullClassName, 5)->create([$dateColumn => '2017-05-26']);

            // Full date range.
            $queryString = '?filter['.$dateColumn.'_between]=2017-02-12,2017-04-21';
            $response = $this->callRoute($queryString);
            $response->assertStatus(200)->assertJsonCount($isNullable ? 3 : 7, 'data');
            if ($isNullable) {
                $this->assertEquals($models3->fresh()->toArray(), $response->json('data'));
            } else {
                $this->assertEquals($models3->merge($models4)->fresh()->toArray(), $response->json('data'));
            }

            // Start date only.
            $queryString = '?filter['.$dateColumn.'_between]=2017-02-12,null';
            $response = $this->callRoute($queryString);
            $response->assertStatus(200)->assertJsonCount($isNullable ? 8 : 12, 'data');
            if ($isNullable) {
                $this->assertEquals($models3->merge($models5)->fresh()->toArray(), $response->json('data'));
            } else {
                $this->assertEquals($models3->merge($models4)->merge($models5)->fresh()->toArray(), $response->json('data'));
            }

            // End date only.
            $queryString = '?filter['.$dateColumn.'_between]=null,2017-04-21';
            $response = $this->callRoute($queryString);
            $response->assertStatus(200)->assertJsonCount($isNullable ? 6 : 10, 'data');
            if ($isNullable) {
                $this->assertEquals($models1->merge($models2)->merge($models3)->fresh()->toArray(), $response->json('data'));
            } else {
                $this->assertEquals($models1->merge($models2)->merge($models3)->merge($models4)->fresh()->toArray(), $response->json('data'));
            }

            $this->emptyResourceModelTable();
        }
    }
}
