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
        if(config('UCC.type') == 'RED') {
            if(isset($data['vip_level'])) {
                if($data['vip_level'] == 5 || $data['vip_level'] == 6) {
                    $data['membership'] = 'Premium';
                }
                elseif($data['vip_level'] == 3 || $data['vip_level'] == 4) {
                    $data['membership'] = 'Preferred';
                }
                elseif($data['vip_level'] == 1 || $data['vip_level'] == 2) {
                    $data['membership'] = 'Privilege';
                }
                else $data['membership'] = 'Classic';
            }
        }

        return $data;
    }
}
