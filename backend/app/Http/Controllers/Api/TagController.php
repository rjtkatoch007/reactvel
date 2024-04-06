<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use App\Http\Resources\TagResource;
use App\Http\Controllers\Controller;

class TagController extends Controller
{
    //fetch all tags
    public function index(){
        
        return TagResource::collection(Tag::all());
    }
}
