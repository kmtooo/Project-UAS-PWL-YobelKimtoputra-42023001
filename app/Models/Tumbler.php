<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class Tumbler extends Model
{
    // use HasFactory;
    use SoftDeletes;
    protected $table = 'tumbler';
    protected $fillable = [
        'tumbler_name',
        'description',
        'price',
        'created_at',
        'created_by',
        'updated_by',
        'updated_at',
    ];
    public function data_adder(){
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}