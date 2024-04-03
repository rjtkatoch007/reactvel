<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\ArticleResource;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->id,
            'name'=>$this->name,
            'email'=>$this->email,
            'bio'=>$this->bio,
            'image_path'=>$this->image_path,
            'followers'=>$this->followers,
            'following'=>$this->following,
            'articles'=>ArticleResource::collection($this->articles),            
        ];
    }
}
