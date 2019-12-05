<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
	protected $fillable = [
		'ar_title', 'en_title', 'ar_content', 'en_content', 'active'
	];
}
