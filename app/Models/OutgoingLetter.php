<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OutgoingLetter extends Model
{
    //
    protected $fillable = [
            'id',               // 👈 เพิ่มบรรทัดนี้
            'doc_number',
            'doc_date',
            'to_agency_id',
            'subject',
            'sender_id',
            'file_path',
            'created_by',
            'updated_by',
        ];

        
            public function toAgency()
        {
            return $this->belongsTo(Department::class, 'to_agency_id');
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
