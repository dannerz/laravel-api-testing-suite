<?php

namespace Dannerz\LaravelApiTestingSuite\Traits;

trait FiltersForeignKeyColumns
{
    protected $filterForeignKeyColumns = [
        // relationship => isNullable : boolean
    ];

    /** @test */
    function filters_foreign_key_columns()
    {
        // TODO: Doesn't accommodate foreign keys that belong to the same table. E.g: `estimates`.`estimate_id`.

        foreach ($this->filterForeignKeyColumns as $relationship => $isNullable) {

            $query = $this->resourceModel->$relationship();

            $foreignKey = $query->getForeignKey();
            $relatedModel = $query->getRelated();

            $relatedModel1 = factory(get_class($relatedModel))->create();
            $relatedModel2 = factory(get_class($relatedModel))->create();
            $relatedModel3 = factory(get_class($relatedModel))->create();
            $relatedModel4 = factory(get_class($relatedModel))->create();
            $relatedModel5 = factory(get_class($relatedModel))->create();

            $models1 = factory($this->resourceModelFullClassName, 1)->create([$foreignKey => $relatedModel1->getKey()]);
            $models2 = factory($this->resourceModelFullClassName, 2)->create([$foreignKey => $relatedModel2->getKey()]);
            $models3 = factory($this->resourceModelFullClassName, 3)->create([$foreignKey => $relatedModel3->getKey()]);
            $models4 = factory($this->resourceModelFullClassName, 4)->create([$foreignKey => $isNullable ? null : $relatedModel4->getKey()]);
            $models5 = factory($this->resourceModelFullClassName, 5)->create([$foreignKey => $relatedModel5->getKey()]);

            $queryString = '?filter['.$foreignKey.']='.$relatedModel1->getKey().','.($isNullable ? 'null' : $relatedModel4->getKey());

            $response = $this->callRoute($queryString);

            $response->assertStatus(200)->assertJsonCount(5, 'data');

            $this->assertEquals($models1->merge($models4)->fresh()->toArray(), $response->json('data'));

            $this->emptyResourceModelTable();
        }
    }
}
