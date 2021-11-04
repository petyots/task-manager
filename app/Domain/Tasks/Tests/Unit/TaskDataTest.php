<?php

namespace App\Domain\Tasks\Tests\Unit;

use App\Domain\Tasks\DataTransferObjects\TaskData;
use Illuminate\Contracts\Support\Arrayable;
use PHPUnit\Framework\TestCase;

class TaskDataTest extends TestCase
{
    public function testTaskDataTransferObjectImplementsArrayable()
    {
        $this->assertTrue(
            condition: in_array(
                needle: Arrayable::class,
                haystack: class_implements(object_or_class: TaskData::class)
            ));
    }

    public function testTaskDataHasToArrayMethod()
    {
        $this->assertTrue(method_exists(TaskData::class, 'toArray'));
    }

    public function testTaskDataToArrayMethodReturnsArray()
    {
        $data = new TaskData(
            uuid: 'test',
            name: null,
            userId: null,
            status: null
        );

        $this->assertIsArray($data->toArray());
    }

    public function testTaskDataToArrayMethodReturnNonEmptyArray()
    {
        $data = new TaskData(
            uuid: 'test',
            name: null,
            userId: null,
            status: null
        );

        $array = $data->toArray();

        $this->assertArrayHasKey('uuid', $array);
        $this->assertArrayHasKey('name', $array);
        $this->assertArrayHasKey('userId', $array);
        $this->assertArrayHasKey('status', $array);
    }
}
