<?php

namespace Carloshb\Tests;
use Carloshb\SqliteModelBasics\Contract\ModelContract;
use Carloshb\SqliteModelBasics\Examples\ExampleModel;
use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase {
    public function testClassType(){
        $model = new ExampleModel();
        $this->assertInstanceOf(ModelContract::class, $model);
    }

    public function testCreateExampleModel(){
        $model = new ExampleModel();
        $time = date('Y-m-d H:i:s');
        $data = [
            "name" => $model->getTable(),
            "content" => "this log",
            "created_at" => $time
        ];
        $response = $model->save($data);
        $this->assertEquals(true, $response);
    }

    public function testSearchCreatedExampleModel(){
        $model = new ExampleModel();
        $data = [
            "name" => $model->getTable(),
            "content" => "this log"
        ];
        $response = $model->where([
            'field' => "name",
            'operation' => "=",
            'content' => $model->getTable()
        ])->get();
        $this->assertArraySubset($data, $response);
        $find = $model->find($response['id']);
        $this->assertEquals($response, $find);
    }

    public function testUpdateExampleModel(){
        $model = new ExampleModel();
        $response = $model->where([
            'field' => "name",
            'operation' => "=",
            'content' => $model->getTable()
        ])->update(
            [
                'content' => "this is a log"
            ]
        );
        $this->assertEquals(true, $response);
    }

    public function testDestroyExampleModel(){
        $model = new ExampleModel();
        $data = $model->where([
            'field' => "name",
            'operation' => "=",
            'content' => $model->getTable()
        ])->get();
        $response = $model->destroy($data['id']);
        $this->assertEquals(true, $response);
    }
}