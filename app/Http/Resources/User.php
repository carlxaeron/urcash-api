<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class User extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $data = parent::toArray($request);
        if(isset($data['user_roles'])) {
            $data['roles'] = collect($data['user_roles'])->map(function($v){ return $v['role']; });
            unset($data['user_roles']);
        }

        return $data;
    }
}
