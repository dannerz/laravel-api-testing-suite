<?php

namespace Dannerz\LaravelApiTestingSuite\Traits;

trait Paginates
{
    protected $defaultSize;

    /** @test */
    function paginates_by_default()
    {
        $defaultSize = $this->defaultSize ?: config('json-api-paginate')['default_size'];

        factory($this->resourceModelFullClassName, $defaultSize+5)->create();

        $this->callRoute()
            ->assertStatus(200)
            ->assertJsonCount($defaultSize, 'data');
    }

    /** @test */
    function paginates_by_specified()
    {
        // TODO: Make this more dynamic.

        $models = factory($this->resourceModelFullClassName, 10)->persist();

        $queryString = '?page[number]=2&page[size]=6';

        $response = $this->callRoute($queryString);

        $response
            ->assertStatus(200)
            ->assertJsonCount(4, 'data');

        $this->assertEquals(
            $models->splice(6)->toArray(),
            $response->json('data')
        );
    }
}
