<?php

use Log;

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class userModel extends Model {
	protected $table = 'user';
	protected $connection = 'mysql';
	public $timestamps = false;
	protected $guarded = array();
	//protected $fillable = array('user_name');
	//public $incrementing = false;
	
}

