<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function store(StoreUserRequest $request){
        if($request->validated()){
            //create new user
            User::create([
                'name'=>$request->name,
                'email'=>$request->email,
                'password'=>Hash::make($request->password),
            ]);
            //return the response
            return response()->json([
                'message'=>'Account has been created successfully!'
            ]);
        }
    }
    //Log in the user
    public function auth(AuthUserRequest $request){
        if($request->validated()){
            //create new user
            User::whereEmail($request->email)->first();
            if(!$user || !Hash::check($request->password, $user->password)){
                return response()->json([
                    'error'=>'Credentials do not match any of our records!'
                ]);
            }else{          
            //return the response
            return response()->json([
                'user'=> UserResource::make($user),
                'access_token'=>$user->createToken('new_user')->plainTexttoken
            ]);
            }
        }
    }

    //Logout the user
    public function logout(Request $request){
        //delete the token of currently logged in user
        $request->user()->currentAccessToken()->delete();
        return response()->json([            
            'message'=> 'Logged out successfully.'
        ]);
    }

    //Follow user
    public function follow(Request $request){
        //get the follower and the user he wants to follow
        $follower = User::findOrFail($request->follower_id);
        $following = User::findOrFail($request->following_id);
        //follow the user
        $follower->following()->attach($following);
        return response()->json([            
            'follower'=> UserResource::make($follower),
            'following'=> UserResource::make($following),
        ]);
        
    }

    //Unfollow users
    public function unfollow(Request $request){
        //get the follower and the user he wants to unfollow
        $follower = User::findOrFail($request->follower_id);
        $following = User::findOrFail($request->following_id);
        //unfollow the user
        $follower->following()->detach($following);
        return response()->json([            
            'follower'=> UserResource::make($follower),
            'following'=> UserResource::make($following),
        ]);
        
    }

    //Unfollow users
    public function updateUserInfos(UpdateUserRequest $request){
        if($request->validated()){
            if($request->has('image')){
                //remove prev user image
              if(File::exists($request->user()->image)){
                File::delete($request->user()->image);
              }  
            //save the new user image & get the file name
            $file = $request->file('image');
            $request->user()->image = 'storage/users/images/'.$this->saveImage($file);
            }           

            //update the user
            $request->user()->update([
                'name' => $request->name,
                'email' => $request->email,
                'bio' => $request->bio               
            ]);            
            return response()->json([
                'user' => UserResource::make($request->user()),
                'message'=>'Your profile has been updated successfully!'
            ]);
        }
    }

    //save images in storage
    public function saveImage($file){
        $file_name = time().'_'.'user'.'_'.$file->getClientOriginalName();
        $file->storeAs('users/image', $file_name, 'public');
        return $file_name;
    }

    //Update users password
    public function updateUserPassword(Request $request){
        //validate the data
        $this->validate($request, [
            'currentPassword' => 'required|min:6|max:255',
            'newPassword' => 'required|min:6|max:255'
        ]);   
        //check if the current password is same as stored one
        if(!Hash::check($request->currenPassword, $request->user()->password)){     
            //if not the same
            return response()->json([                
                'error'=>'The current password is incorrect!'
            ]);
        } else {
            //update the user password
            $request->user()->update([
                'password' => Hash::make($request->password)                            
            ]);            
            return response()->json([                
                'message'=>'Your password has been updated successfully!'
            ]);
        }   
    }    
}
