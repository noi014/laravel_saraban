<?php
// app/Models/OfficialLetter.php
namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class OfficialLetter extends Model
{
    use HasFactory;

    protected $fillable = [
        'reg_number', 
        'reg_date', 'doc_number', 'doc_date',
        'from_agency', 'to_agency', 'subject', 'receiver_department',
         'created_by','updated_by',
    ];
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    protected $casts = [
        'to_agency' => 'array',
    ];
    public function attachments()
    {
        return $this->hasMany(OfficialLetterAttachment::class);
    }

    public function department()
    {
    return $this->belongsTo(Department::class);
    }  
    
    public function receivingAgencies()
    {
        return $this->belongsToMany(ReceivingAgency::class, 'official_letter_receiving_agency');
    }

 
}
