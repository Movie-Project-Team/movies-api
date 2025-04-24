<?php

namespace App\Models\DB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovieLanguage extends Model
{
    use HasFactory;

    protected $table = 'movie_language';

    protected $fillable = [
        'movie_id',
        'language_id',
    ];
}
