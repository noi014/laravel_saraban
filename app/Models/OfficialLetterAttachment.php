<?php
// app/Models/OfficialLetter.php
namespace App\Models;
// app/Models/OfficialLetterAttachment.php
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class OfficialLetterAttachment extends Model
{
    use HasFactory;

    protected $fillable = ['official_letter_id', 'file_path'];

    public function letter()
    {
        return $this->belongsTo(OfficialLetter::class);
    }
}
