<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\PostResource;
class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
   
            'uuid'=>$this->uuid,
             'content'=>$this->content,
             'user'=>$this->user->name,
            'post'=>PostResource::make($this->post)
        ];
    }
}
