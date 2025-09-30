<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CsvMapper extends Model
{
    protected $fillable = [
        'user_id',
        'column_mapping',
        'is_default',
        'temp_file_path'
    ];

    protected $casts = [
        'column_mapping' => 'array',
        'is_default' => 'boolean',
    ];
}
