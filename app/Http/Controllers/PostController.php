<?php

namespace App\Http\Controllers;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Http\Traits\GeneralTrait;
use Illuminate\Http\Request;

class PostController extends Controller
{
    use GeneralTrait;

    /**
     * @OA\Get(
     *      path="/api/read/posts",
     *      operationId="index",
     *      tags={"Post"},
     *      summary="Get all posts for Rest Api",
     *      @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     *      
     *   
     * )
     */


    public function index()
    {
       
        $posts=Post::all();        
        $posts=PostResource::collection($posts);
        return $this->apiResponse($posts) ;  


    }

/**
     * @OA\Post(
     *   path="/api/post/create",
     *   tags={"Post"},
     *   operationId="store",
     *   summary="Create Post",
     *     @OA\Parameter(
        *      name="title",
         *     in="query",
         *      description="Post title",
        *      required=true,
         *    @OA\Schema(type="string")
        *     ),
         *     @OA\Parameter(
        *         name="content",
        *         in="query",
        *         description="Post content",
         *         required=true,
         *         @OA\Schema(type="string")
        *     ),
        *   @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     *  )
    
     */

   
    public function store(Request $request)
    {   

        $validate = Validator::make($request->all(),[
            'title' => 'required|string|max:50|min:1',
            'content' => 'required|string|max:500|min:2',
            ]);
            if($validate->fails()){
            return $this->requiredField($validate->errors()->first());    
            }
        
           $data=$request->all();
           $data['uuid']=Str::uuid();
           $data['user_id']=Auth::id();
           $post=Post::create($data);
           $post=PostResource::make($post);
           return $this->apiResponse($post) ;  

    }

/**
     * @OA\Post(
     *   path="/api/post/edit",
     *   tags={"Post"},
     *   operationId="update",
     *   summary="Update Post",
     *     @OA\Parameter(
     *         name="uuid",
     *         in="query",
     *         description="uuid",
     *         required=true,
     *      ),
     * 
     *    @OA\Parameter(
        *      name="title",
         *     in="query",
         *    description="title",
         *    @OA\Schema(type="string")
        *     ),

        *     @OA\Parameter(
        *         name="content",
        *         in="query",
        *         description="content",
         *         @OA\Schema(type="string")
        *     ),

    *   @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     *  )
    
     */
    

    public function update(Request $request)
     
    {
 
        $validate = Validator::make($request->all(),[
            'title' => 'string|max:50|min:1',
            'content' => 'string|max:500|min:2',
            ]);
            if($validate->fails()){
            return $this->requiredField($validate->errors()->first());    
            }
            $post=Post::where('uuid',$request->uuid)->first();
            if(!$post)
            {
                return $this->apiResponse('not found') ;  

            }
           if($post->user_id!=Auth::id())
           {
            return $this->unAuthorizeResponse('not unAuthorized');
           }
          $post =$post->update($request->only('title','content'));
       
          return $this->apiResponse('succsess update') ;  


    }
/**
     * @OA\Get(
     *   path="/api/post/delete",
     *   tags={"Post"},
     *   operationId="destroy",
     *   summary="Delete Post",
     *     @OA\Parameter(
     *         name="uuid",
     *         in="query",
     *         description="uuid",
     *         required=true,
     *      ),
     * 
     *   @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     *  )
    
     */
    
    
    public function destroy(Request $request)
    
    {
        
     $post=Post::where('uuid',$request->uuid)->first();

    if(!$post) 
    {
    
    return $this->apiResponse('not found') ;  
    }
   if($post->user_id!=Auth::id())
   {
      return $this->unAuthorizeResponse('not unAuthorized');
   }


 
    $post->delete();
    return $this->apiResponse('succsess delete post') ;  
  






    }
}
