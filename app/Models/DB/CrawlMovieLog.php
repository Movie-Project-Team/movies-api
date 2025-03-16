<?php

namespace App\Models\DB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrawlMovieLog extends Model
{
    use HasFactory;

    protected $table = 'crawl_movies_log';

    protected $fillable = ['date', 'total_movies', 'success', 'failed', 'success_rate'];
}
