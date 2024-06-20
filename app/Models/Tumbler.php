<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use OpenApi\Annotations as OA;


/**
 * Class Tumbler.
 * 
 * @author Yobel <yobel.422023001@civitas.ukrida.ac.id>
 * 
 * @OA\Schema(
 *     description="Tumbler model",
 *     title="Tumbler model",
 *     required={"title", "author"},
 *     @OA\Xml(
 *         name="Tumbler"
 *     )
 * )
 */
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
        'publication_year',
        'pict',
    ];
    public function data_adder(){
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}