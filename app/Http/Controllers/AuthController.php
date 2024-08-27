<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Traits\GeneralTrait;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\UserResource;
use Illuminate\Support\Str;



class AuthController extends Controller
   
   {
    use GeneralTrait;

/**
 * @OA\Post(
 *     path="/api/register",
 *         tags={"Auth"},
 *      operationId="register",
 *     summary="Register a new user",
 *     @OA\Parameter(
 *         name="name",
 *         in="query",
 *         description="User's name",
 *         required=true,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *         name="email",
 *         in="query",
 *         description="User's email",
 *         required=true,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *         name="password",
 *         in="query",
 *         description="User's password",
 *         required=true,
 *         @OA\Schema(type="string")
 *     ),

 *     @OA\Response(response="201", description="User registered successfully"),
 *     @OA\Response(response="422", description="Validation errors")
 * )
 */

 public function register(Request $request)
   
 {

  $validate = Validator::make($request->all(),[
    'name' => 'required|string',
    'email' => 'required|string|unique:users,email',
    'password' => 'required|string'
    ]);
    if($validate->fails()){
    return $this->requiredField($validate->errors()->first());    
    }

  
  $user = User::create([
     'uuid'=>Str::uuid(),
      'name' => $request->name,
      'email' => $request->email,
      'password' => Hash::make($request->password)
    ]);

    $token = $user->createToken('apiToken')->accessToken;

  $res = [
      'user' =>UserResource::make($user),
      'token' => $token
  ];
  return $this->apiResponse($res);


}


/**
* @OA\Post(
*     path="/api/login",
*       tags={"Auth"},
*      operationId="login",
*     summary="Authenticate user and generate JWT token",
*     @OA\Parameter(
*         name="email",
*         in="query",
*         description="User's email",
*         required=true,
*         @OA\Schema(type="string")
*     ),
*     @OA\Parameter(
*         name="password",
*         in="query",
*         description="User's password",
*         required=true,
*         @OA\Schema(type="string")
*     ),
*     @OA\Response(response="200", description="Login successful"),
*     @OA\Response(response="401", description="Invalid credentials")
* )
*/

  
    public function login(Request $request)
    
     {
      $validate = Validator::make($request->all(),[
        'email' => 'required|string',
        'password' => 'required|string'
        ]);
        if($validate->fails()){
        return $this->requiredField($validate->errors()->first());    
        }
 
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
          return $this->unAuthorizeResponse('incorrrect username or password');
        }
  
        
        $token= $user->createToken($request->email)->accessToken;
        $res = [
        'user' =>UserResource::make($user),
        'token' => $token
         ];
      return $this->apiResponse($res);

    }

 /**
     * @OA\Post(
     *   path="/api/logout",
     *   tags={"Auth"},
     *   operationId="logout",
     *   summary="Logout",
     *   @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     *  )
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function logout(Request $request)
       
     {
       auth('api')->user()->tokens()->delete();
       return $this->apiResponse('user logged out');

      }


      public function authrise()
       
      {
     
        return $this->unAuthorizeResponse('please log in first!');
 
       }
     

     }
