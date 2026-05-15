<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\FrontendController;


use App\Repositories\Post\PostRepository;
use App\Repositories\Post\PostCatalogueRepository;

use Illuminate\Http\Request;


class PostController extends FrontendController
{
   
    protected $postRepository;
    protected $postCatalogueRepository;

    public function __construct(
        PostRepository $postRepository,
        PostCatalogueRepository $postCatalogueRepository,
    ){
        $this->postRepository = $postRepository;
        $this->postCatalogueRepository = $postCatalogueRepository;
        parent::__construct(); 
    }

   
    public function video(Request $request){
        $id = $request->input('id');

        $post = $this->postRepository->getPostById($id, $this->language);
        $html = $this->renderVideoHtml($post->video);

        return response()->json([
            'html' => $html
        ]);
        
    }

    private function renderVideoHtml($video){
        $explode = explode('/userfiles/flash/', $video);
        $html = '';
        if(count($explode) == 2){
            $html .= '<video width="100%" height="380" controls>';
                $html .= '<source src="'.$video.'" type="video/mp4">';
            $html .= '</video>';
        }else{
            $html .= $video;
        }
        return $html;
    }


    public function loadReview(Request $request){
        $page = $request->input('page', 1);
        $perPage = 4;
        $offset = 6 + $perPage * ($page - 2); // Initial block shows 6 items. Page 2 skips these 6.

        $posts = $this->postRepository->findPosts([
            ['publish', '=', 2],
            ['is_review', '=', 1]
        ], $this->language, ['id', 'DESC'], $perPage, $offset);

        $html = '';
        if(count($posts)){
            $html = view('frontend.component.review_item_horizontal', compact('posts'))->render();
        }

        return response()->json([
            'html' => $html,
            'hasMore' => (count($posts) == $perPage)
        ]);
    }

    public function loadRelated(Request $request)
    {
        $page = $request->input('page', 1);
        $catalogueId = $request->input('catalogueId');
        $perPage = 3;
        $offset = 6 + $perPage * ($page - 2);

        $posts = $this->postRepository->getRelatedPostsByCategory($catalogueId, $this->language, $perPage, $offset);

        $html = '';
        if (count($posts)) {
            $html = view('frontend.component.review_item_horizontal', compact('posts'))->render();
        }

        return response()->json([
            'html' => $html,
            'hasMore' => (count($posts) == $perPage)
        ]);
    }

    public function loadBreakingNews(Request $request)
    {
        $page = $request->input('page', 1);
        $perPage = 6;
        $offset = 7 + $perPage * ($page - 2); // Initial: 1 Featured + 6 Items = 7. Page 2+ skips these 7.

        $posts = $this->postRepository->findPosts([
            ['publish', '=', 2]
        ], $this->language, ['id', 'DESC'], $perPage, $offset);

        $html = '';
        if (count($posts)) {
            $html = view('frontend.post.catalogue.component.breaking_news_item', compact('posts'))->render();
        }

        return response()->json([
            'html' => $html,
            'hasMore' => (count($posts) == $perPage)
        ]);
    }

    public function updateOrder(Request $request){

        $payload['order'] =  $request->input('order');
        unset($payload['product_id']);
        $id = $request->input('product_id');
        $update_order = $this->postCatalogueRepository->update($id, $payload);
        return response()->json([
            'response' => $update_order, 
            'messages' => 'Cập nhật thứ tự thành công',
            'code' => (!$update_order) ? 11 : 10,
        ]);  
    }

}
