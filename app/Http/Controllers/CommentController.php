<?php
namespace App\Http\Controllers;
use App\Http\Resources\CommentResource;
use App\Models\Post;
use App\Models\User;
use App\Models\Comment;
use App\Mail\TestMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Http\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Mail;
class CommentController extends Controller
{
    use GeneralTrait;

  
 /**
     * @OA\Post(
     *   path="/api/comment/add",
     *   tags={"Comment"},
     *   operationId="create",
     *   summary="Create Comment",
     *      @OA\Parameter(
     *      name="post_uuid",
     *     in="query",
     *      description="",
     *      required=true,
     *    @OA\Schema(type="string")
    *     ),
    *     @OA\Parameter(
    *         name="content",
    *         in="query",
    *         description="",
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




    public function create(Request $request)
     
    {   
    
        $validate = Validator::make($request->all(),[
            'post_uuid'=>'required|string|exists:posts,uuid',
            'content' => 'required|string|max:500|min:2',
            ]);

          if($validate->fails()){
            return $this->requiredField($validate->errors()->first());    
            }
           
           $post=Post::where('uuid',$request->post_uuid)->get();
           $post_id=$post->value('id');

           $comment=Comment::create([
           
            'uuid'=>Str::uuid(),
            'user_id'=>Auth::id(),
            'post_id'=>$post_id,
            'content'=>$request->content

           ]);
           $users=Post::find($post_id)->users;
           
           foreach($users as $user)
           {
            
           Mail::to($user->email)->send(new TestMail);
           
           }

           $comment=CommentResource::make($comment);
           return $this->apiResponse($comment) ; 
        } 

   

/**
     * @OA\Get(
     *   path="/api/comment/remove",
     *   tags={"Comment"},
     *   operationId="delete",
     *   summary="Delete Comment",
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


    
      public function delete(Request $request)
    
     {
       
      $comment=Comment::where('uuid',$request->uuid)->first();
   
        if(!$comment) 
       {
    
      return $this->apiResponse('not found') ;  
        }
   
      if($comment->user_id!=Auth::id())
      {
    
        return $this->unAuthorizeResponse('not unAuthorized');
    
       }

          $comment->delete();
          return $this->apiResponse('succsess delete comment') ;  
  
    }
   
  }
