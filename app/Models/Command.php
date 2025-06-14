<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Command extends Model
{
    protected $fillable = [
        'command_number', 'command_name', 'command_date', 'file_path', 'created_by', 'updated_by'
    ];

    public function agencies()
    {
        return $this->belongsToMany(ReceivingAgency::class, 'command_agency', 'command_id', 'department_id');
    }

    public function creator() {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater() {
        return $this->belongsTo(User::class, 'updated_by');
    }
}