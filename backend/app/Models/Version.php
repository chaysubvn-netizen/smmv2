<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Version extends Model
{
    protected $fillable = [
        'version', 'changelog', 'file_url', 'is_latest'
    ];
}
