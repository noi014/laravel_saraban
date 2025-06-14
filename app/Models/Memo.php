<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// class Memo extends Model
// {
//     //
// }
class Memo extends Model
{
    protected $fillable = [
        'memo_number', 'memo_date', 'subject', 'executive_id', 'from_user_id', 'file_path', 'created_by', 'updated_by'
    ];

    public function executive() {
        return $this->belongsTo(Executive::class);
    }

    public function fromUser() {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function creator() {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater() {
        return $this->belongsTo(User::class, 'updated_by');
    }
}

