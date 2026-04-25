<?php

namespace App\Repositories\Post;

use App\Models\Post;
use App\Repositories\Interfaces\PostRepositoryInterface;
use App\Repositories\BaseRepository;

/**
 * Class UserService
 * @package App\Services
 */
class PostRepository extends BaseRepository
{
    protected $model;

    public function __construct(
        Post $model
    ){
        $this->model = $model;
        parent::__construct($model);
    }

    

    public function getPostById(int $id = 0, $language_id = 0){
        return $this->model->select([
                'posts.id',
                'posts.post_catalogue_id',
                'posts.image',
                'posts.icon',
                'posts.album',
                'posts.publish',
                'posts.follow',
                'posts.video',
                'posts.template',
                'posts.created_at',
                'posts.viewed',
                'posts.status_menu',
                'posts.short_name',
                'posts.logo',
                'posts.extra',
                'posts.comments',
                'posts.rate',
                'posts.post_type',
                'posts.recommend',
                'posts.product_id',
                'posts.is_review',
                'tb2.name',
                'tb2.description',
                'tb2.content',
                'tb2.meta_title',
                'tb2.meta_keyword',
                'tb2.meta_description',
                'tb2.canonical',
            ]
        )
        ->join('post_language as tb2', 'tb2.post_id', '=','posts.id')
        ->with('post_catalogues')
        ->where('tb2.language_id', '=', $language_id)
        ->find($id);
    }

    public function findPosts(array $condition = [], int $languageId = 1, array $orderBy = ['id', 'DESC'], int $limit = 10){
        return $this->model->where($condition)
        ->whereHas('languages', function($query) use ($languageId){
            $query->where('language_id', $languageId);
        })
        ->with(['languages' => function($query) use ($languageId){
            $query->where('language_id', $languageId);
        }])
        ->orderBy($orderBy[0], $orderBy[1])
        ->limit($limit)
        ->get();
    }

    public function findPostsPagination(array $condition = [], int $languageId = 1, array $orderBy = ['id', 'DESC'], int $perPage = 6, int $page = 1){
        return $this->model->where($condition)
        ->whereHas('languages', function($query) use ($languageId){
            $query->where('language_id', $languageId);
        })
        ->with(['languages' => function($query) use ($languageId){
            $query->where('language_id', $languageId);
        }])
        ->orderBy($orderBy[0], $orderBy[1])
        ->paginate($perPage, ['*'], 'page', $page);
    }

    public function search(?string $keyword = '', int $languageId = 1, int $perPage = 12){
        return $this->model->select([
            'posts.id',
            'posts.image',
            'posts.created_at',
            'posts.viewed',
            'tb2.name',
            'tb2.canonical',
            'tb2.description'
        ])
        ->join('post_language as tb2', 'tb2.post_id', '=', 'posts.id')
        ->where('tb2.language_id', '=', $languageId)
        ->where('posts.publish', '=', 2)
        ->where(function($query) use ($keyword){
            $query->where('tb2.name', 'LIKE', '%'.$keyword.'%')
                  ->orWhere('tb2.description', 'LIKE', '%'.$keyword.'%')
                  ->orWhere('tb2.content', 'LIKE', '%'.$keyword.'%');
        })
        ->orderBy('posts.id', 'DESC')
        ->paginate($perPage);
    }

}
