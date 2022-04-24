<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
class Pet extends Model
{
    use HasFactory;
    protected $table="pets";

    protected $fillable =[
        'category',
        'name',
        'photoUrls',
        'tags',            
        'status',
    ];

      protected $casts = [
        'photoUrls' => 'array',
   ];  
    protected $hidden = ["created_at", "updated_at"];
 
      protected function photoUrls(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value,true),
            set: fn ($value) => json_encode($value,true),
        );
    } 

    protected function tags(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value,true),
            set: fn ($value) => json_encode($value,true),
        );
    } 

    protected function category(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value,true),
            set: fn ($value) => json_encode($value,true),
        );
    } 
    
}
