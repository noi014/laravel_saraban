<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SendingLetter extends Model
{
    protected $fillable = [
        'doc_number', 'doc_date', 'subject',
        'sender_id', 'file_path', 'created_by', 'updated_by'
    ];

    public function agencies()
    {
        
        return $this->belongsToMany(Department::class, 'sending_letter_receiving_agency');
    }

    public function sender()
    {
        return $this->belongsTo(Executive::class, 'sender_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}

