<?php
// app/Models/ReceivingAgency.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReceivingAgency extends Model
{
    protected $fillable = ['name'];

    public function officialLetters()
    {
        return $this->belongsToMany(OfficialLetter::class, 'official_letter_receiving_agency');
    }
}
