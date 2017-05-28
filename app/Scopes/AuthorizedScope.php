<?php
namespace App\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use IndieAuth;
use DB;
use App\PersonUrl;

class AuthorizedScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        if(IndieAuth::is_logged_in()){
            $owner = trim(config('splatter.owner.url'), '/');
            if(IndieAuth::is_user($owner)) {
                $builder;
            } else {
                $person_url = PersonUrl::where('url', IndieAuth::user())->get()->first();

                if(empty($person_url)){
                    $builder->doesntHave('acls');
                } else {
                    $builder->whereDoesntHave('acls', function($query) use ($person_url){
                        $query->where('person_id', '<>', $person_url->person_id);
                    });
                }
                
            }
        } else {
            $builder->doesntHave('acls');
        }
    }

}
