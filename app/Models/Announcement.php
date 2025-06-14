<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = [
        'announcement_number', 'announcement_name', 'announcement_date',
        'file_path', 'created_by', 'updated_by'
    ];

    // public function agencies()
    // {
    //     return $this->belongsToMany(ReceivingAgency::class, 'announcement_agency');
    // }
    public function agencies()
{
    return $this->belongsToMany(ReceivingAgency::class, 'announcement_agency', 'announcement_id', 'agency_id');
}

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
