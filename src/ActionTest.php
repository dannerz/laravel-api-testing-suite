<?php

namespace Dannerz\LaravelApiTestingSuite;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

abstract class ActionTest extends TestCase
{
    use DatabaseTransactions;

    protected $prefix = 'api';

    protected $method;

    protected $resource;

    protected $modelNamespace = 'App\Models';

    protected $userModelClassName = 'User';

    protected $resourceModelClassName;

    protected $resourceModelFullClassName;

    protected $resourceModel;

    protected function setUp()
    {
        if ($this->resourceModelClassName) {
            $this->resourceModelFullClassName = $this->modelNamespace.'\\'.$this->resourceModelClassName;
            $this->resourceModel = new $this->resourceModelFullClassName();
        }

        parent::setUp();
    }

    protected function callRoute($path = null, $data = [], $user = null)
    {
        $this->actingAs($user ?: factory($this->modelNamespace.'\\'.$this->userModelClassName)->create());

        $uri = $this->prefix.'/'.$this->resource.($path ? '/'.$path : '');

        return $this->json($this->method, $uri, $data);
    }

    protected function callRouteWithoutPath($data, $actingAs = null)
    {
        return $this->callRoute(null, $data, $actingAs);
    }

    protected function callRouteWithoutData($path, $actingAs)
    {
        return $this->callRoute($path, [], $actingAs);
    }

    protected function emptyResourceModelTable()
    {
        if ($this->resourceModel) {
            DB::table($this->resourceModel->getTable())->delete();
        }
    }

    protected function assertDatabaseHas($table, array $data, $connection = null)
    {
        // Parse JSON values.
        foreach ($data as $property => $value) {
            if (is_string($value)) {
                $value = json_decode($value);
            }
            if (is_array($value) || is_object($value)) {
                $json = json_encode($value);
                $data[$property] = DB::raw("CAST('{$json}' AS JSON)");
            }
        }

        return parent::assertDatabaseHas($table, $data, $connection);
    }
}
