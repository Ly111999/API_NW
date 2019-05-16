<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Job extends Model
{
    use SoftDeletes;

    protected $table = 'jobs';
    protected $dates = ['deleted_at'];

    public static function to_slug($str)
    {
        $str = trim(mb_strtolower($str));
        $str = preg_replace('/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/', 'a', $str);
        $str = preg_replace('/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/', 'e', $str);
        $str = preg_replace('/(ì|í|ị|ỉ|ĩ)/', 'i', $str);
        $str = preg_replace('/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/', 'o', $str);
        $str = preg_replace('/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/', 'u', $str);
        $str = preg_replace('/(ỳ|ý|ỵ|ỷ|ỹ)/', 'y', $str);
        $str = preg_replace('/(đ)/', 'd', $str);
        $str = preg_replace('/[^a-z0-9-\s]/', '', $str);
        $str = preg_replace('/([\s]+)/', '-', $str);
        return $str;
    }

    public static function createNewJob($request,$userId){
        $data = new Job();
        $data->title = $request['title'];
        $data->location = $request['location'];
        $data->company = $request['company'];
        $data->description = $request['description'];
        $url = $data->title . " " . $data->id;
        $data->slug = self::to_slug($url);
        $data->user_id = $userId;
        $data->save();

        return $data;
    }
    public static function updateJob($request, $data ){
        $data->title = $request['title'];
        $data->location = $request['location'];
        $data->company = $request['company'];
        $data->description = $request['description'];
        $url = $data->title . " " . $data->id;
        $data->slug = self::to_slug($url);
        $data->save();
        return $data;
    }
}
