<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    protected $fillable = [
        'name',
        'name_ar',
        'slug',
        'parent_id',
        'position',
        'is_active',
        'image',
        'icon',
        'is_featured'
    ];


    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }


    function allChildrenIds() : array
    {
        $ids = [];

        foreach($this->children as $child) {
            $ids[] = $child->id;

            $ids = array_merge($ids, $child->allChildrenIds());
        }

        return $ids;
    }



    static function getNested($parentId = null, $depth = 0, $maxDepth = 3)
    {
        if($depth >= $maxDepth) return [];
        $categories = self::where('parent_id', $parentId)->orderBy('position')->get();

        foreach ($categories as $cat) {
            $cat->children_nested = self::getNested($cat->id, $depth + 1, $maxDepth);
        }

        return $categories;

    }

    function products() : BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }
}
