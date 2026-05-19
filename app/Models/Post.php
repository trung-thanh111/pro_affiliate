<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;
use App\Traits\QueryScopes;

class Post extends Model
{
    use HasFactory, SoftDeletes, QueryScopes;

    protected $fillable = [
        'image',
        'icon',
        'banner',
        'album',
        'publish',
        'follow',
        'order',
        'user_id',
        'post_catalogue_id',
        'video',
        'template',
        'viewed',
        'status_menu',
        'short_name',
        'logo',
        'extra',
        'comments',
        'rate',
        'recommend',
        'post_type',
        'released_at',
        'files',
        'is_review',
        'product_id'
    ];

    protected $table = 'posts';

    protected $with = ['post_catalogues'];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function languages(){
        return $this->belongsToMany(Language::class, 'post_language' , 'post_id', 'language_id')
        ->withPivot(
            'name',
            'canonical',
            'meta_title',
            'meta_keyword',
            'meta_description',
            'description',
            'content'
        )->withTimestamps();
    }

    public function post_catalogues(){
        return $this->belongsToMany(PostCatalogue::class, 'post_catalogue_post' , 'post_id', 'post_catalogue_id');
    }

    public function related_posts(){
        return $this->belongsToMany(Post::class, 'post_related', 'post_id', 'related_id');
    }

    protected $casts = [
        'released_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function post_products()
    {
        return $this->hasMany(PostProduct::class, 'post_id', 'id')->orderBy('sort_order');
    }

    public function comparison_sections()
    {
        return $this->hasMany(ComparisonSection::class, 'post_id', 'id')->orderBy('sort_order');
    }

    /**
     * Get the dynamically resolved affiliate redirect link for the post.
     *
     * @return string|null
     */
    public function getAffiliateUrlAttribute()
    {
        // 1. Direct Product
        if ($this->relationLoaded('product') && $this->product && !empty($this->product->link)) {
            return $this->product->link;
        }
        
        $product = $this->product;
        if ($product && !empty($product->link)) {
            return $product->link;
        }

        // 2. Comparison Products
        if ($this->relationLoaded('post_products') && $this->post_products->count() > 0) {
            $validPostProducts = $this->post_products->filter(function($pp) {
                return $pp->product && !empty($pp->product->link);
            });
            if ($validPostProducts->count() > 0) {
                return $validPostProducts->random()->product->link;
            }
        } else {
            $validPostProduct = $this->post_products()->whereHas('product', function($query) {
                $query->whereNotNull('link')->where('link', '!=', '');
            })->with('product')->get();
            if ($validPostProduct->count() > 0) {
                return $validPostProduct->random()->product->link;
            }
        }

        // 3. Fallback: Cached random product link from the DB
        return cache()->remember('fallback_affiliate_url_' . $this->id, 60, function() {
            $randomProduct = \App\Models\Product::whereNotNull('link')
                ->where('link', '!=', '')
                ->inRandomOrder()
                ->first();
            return $randomProduct ? $randomProduct->link : null;
        });
    }


}
