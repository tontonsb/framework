<?php

namespace Illuminate\Tests\Database;

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\TestCase;

class DatabaseEloquentHasOneOrManyWithAttributesTest extends TestCase
{
    protected function setUp(): void
    {
        $db = new DB;

        $db->addConnection([
            'driver' => 'sqlite',
            'database' => ':memory:',
        ]);
        $db->bootEloquent();
        $db->setAsGlobal();
    }

    public function testHasManyAddsAttributes(): void
    {
        $parentId = 123;
        $key = 'a key';
        $value = 'the value';

        $parent = new RelatedWithAttributesModel;
        $parent->id = $parentId;

        $relationship = $parent
            ->hasMany(RelatedWithAttributesModel::class, 'parent_id')
            ->withAttributes([$key => $value]);

        $relatedModel = $relationship->make();

        $this->assertSame($parentId, $relatedModel->parent_id);
        $this->assertSame($value, $relatedModel->$key);
    }

    public function testHasOneAddsAttributes(): void
    {
        $parentId = 123;
        $key = 'a key';
        $value = 'the value';

        $parent = new RelatedWithAttributesModel;
        $parent->id = $parentId;

        $relationship = $parent
            ->hasOne(RelatedWithAttributesModel::class, 'parent_id')
            ->withAttributes([$key => $value]);

        $relatedModel = $relationship->make();

        $this->assertSame($parentId, $relatedModel->parent_id);
        $this->assertSame($value, $relatedModel->$key);
    }

    public function testMorphManyAddsAttributes(): void
    {
        $parentId = 123;
        $key = 'a key';
        $value = 'the value';

        $parent = new RelatedWithAttributesModel;
        $parent->id = $parentId;

        $relationship = $parent
            ->morphMany(RelatedWithAttributesModel::class, 'relatable')
            ->withAttributes([$key => $value]);

        $relatedModel = $relationship->make();

        $this->assertSame($parentId, $relatedModel->relatable_id);
        $this->assertSame($parent::class, $relatedModel->relatable_type);
        $this->assertSame($value, $relatedModel->$key);
    }

    public function testMorphOneAddsAttributes(): void
    {
        $parentId = 123;
        $key = 'a key';
        $value = 'the value';

        $parent = new RelatedWithAttributesModel;
        $parent->id = $parentId;

        $relationship = $parent
            ->morphOne(RelatedWithAttributesModel::class, 'relatable')
            ->withAttributes([$key => $value]);

        $relatedModel = $relationship->make();

        $this->assertSame($parentId, $relatedModel->relatable_id);
        $this->assertSame($parent::class, $relatedModel->relatable_type);
        $this->assertSame($value, $relatedModel->$key);
    }

    public function testWithAttributesCanBeOverriden(): void
    {
        $key = 'a key';
        $defaultValue = 'a value';
        $value = 'the value';

        RelatedWithAttributesModel::unguard();
        $parent = new RelatedWithAttributesModel;

        $relationship = $parent
            ->hasMany(RelatedWithAttributesModel::class, 'relatable')
            ->withAttributes([$key => $defaultValue]);

        $relatedModel = $relationship->make([$key => $value]);

        $this->assertSame($value, $relatedModel->$key);
    }
}

class RelatedWithAttributesModel extends Model {}
