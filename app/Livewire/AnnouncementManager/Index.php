<?php

namespace App\Livewire\AnnouncementManager;

use Livewire\Component;
use App\Models\Announcement;
use App\Models\ReceivingAgency;
use Illuminate\Support\Facades\Auth;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class Index extends Component
{
      use WithFileUploads;
    use WithPagination;
    // public function render()
    // {
    //     return view('livewire.announcement-manager.index');
    // }
    public $searchTerm, $searchDate, $modalOpen = false;
public $announcementId, $announcement_number, $announcement_name, $announcement_date, $letterId;
public $file_path, $selected_agencies = [];
public $file;
public  $showModal = false;
public $receivingAgencyOptions = [];
public $searchCreatedByAgency = null;
protected $listeners = ['confirm-delete' => 'delete'];
 public function delete($id)
    {
        $letter = Announcement::findOrFail($id);
        if ($letter->file_path) {
            Storage::disk('public')->delete($letter->file_path);
        }
        $letter->delete();
         $this->js(<<<JS
            Swal.fire({
                icon: 'success',
                title: 'ลบสำเร็จ',
                text: 'ข้อมูลถูกลบเรียบร้อยแล้ว',
                toast: true,
                position: 'top-end',
                timer: 2000,
                showConfirmButton: false
            })
        JS);
    }
  public function search()
    {
        $this->resetPage(); // รีเซ็ต pagination
    }
public function render()
{
     if (Auth::user()->role === 'admin'||Auth::user()->receiving_agency_id === 1) {
        $this->receivingAgencyOptions = \App\Models\ReceivingAgency::orderBy('name')->get();
        } 
    $query = Announcement::with('agencies');
     // ถ้าไม่ใช่ admin ให้ filter หน่วยงานของตัวเอง
    if (Auth::user()->receiving_agency_id !== 1) 
    {
        $userDepartmentId = Auth::user()->receiving_agency_id;

        $query->whereHas('agencies', function ($q) use ($userDepartmentId) {
            $q->where('receiving_agencies.id', $userDepartmentId);
        });
    }
   
    // ค้นหาจากหน่วยงาน (เฉพาะตอน admin หรือผู้ใช้ที่เลือก filter)
    if ($this->searchCreatedByAgency) {
        $query->whereHas('agencies', function ($q) {
            $q->where('receiving_agencies.id', $this->searchCreatedByAgency);
        });
    }

    if ($this->searchTerm) {
        $query->where('announcement_number', 'like', "%{$this->searchTerm}%")
              ->orWhere('announcement_name', 'like', "%{$this->searchTerm}%");
    }

    if ($this->searchDate) {
        $query->whereDate('announcement_date', $this->searchDate);
    }

   

    $announcements = $query->latest()->paginate(5);

    return view('livewire.announcement-manager.index', [
        'announcements' => $announcements,
        'agencyOptions' => ReceivingAgency::all(),
    ]);
}
 public function create()
    {  
        $this->reset(['announcementId', 'announcement_number', 'announcement_name', 'announcement_date', 'file_path', 'selected_agencies']);
               
        $this->announcement_date = \Carbon\Carbon::now()->format('Y-m-d');
           // เคลียร์ validation error ต่าง ๆ
        $this->resetErrorBag();
        $this->resetValidation();
        $this->showModal = true;
    }

      public function edit($id)
    {
        $data = Announcement::with('agencies')->findOrFail($id);
        $this->announcementId = $data->id;
        $this->announcement_number = $data->announcement_number;
        $this->announcement_name = $data->announcement_name;
        $this->announcement_date = $data->announcement_date;
        $this->selected_agencies = $data->agencies->pluck('id')->toArray();
       //  $this->letterId = $id;
        // เคลียร์ validation error ต่าง ๆ
        $this->resetErrorBag();
        $this->resetValidation();
        $this->showModal = true;
    }
// public function openModal($id = null)
// {
//     $this->reset(['announcementId', 'announcement_number', 'announcement_name', 'announcement_date', 'file_path', 'selected_agencies']);
//     if ($id) {
//         $data = Announcement::with('agencies')->findOrFail($id);
//         $this->announcementId = $data->id;
//         $this->announcement_number = $data->announcement_number;
//         $this->announcement_name = $data->announcement_name;
//         $this->announcement_date = $data->announcement_date;
//         $this->selected_agencies = $data->agencies->pluck('id')->toArray();
//     }
//     $this->modalOpen = true;
// }

public function save()
{
    $this->validate([
        'announcement_number' => 'required',
        'announcement_name' => 'required',
        'announcement_date' => 'required|date',
        'selected_agencies' => 'required|array|min:1',
          'file' => 'nullable|file|max:20480',
      //  'file' => 'nullable|file|max:20480|mimes:pdf,doc,docx',
    ]);

    $data = [
        'announcement_number' => $this->announcement_number,
        'announcement_name' => $this->announcement_name,
        'announcement_date' => $this->announcement_date,
    ];
    if ($this->announcementId) {
        $existingLetter = \App\Models\Announcement::find($this->letterId);
    } else {
        $existingLetter = null;
    }
     // 👉 ตรวจสอบและอัปโหลดไฟล์ใหม่
    if ($this->file instanceof UploadedFile) {
        if ($existingLetter && $existingLetter->file && Storage::disk('public')->exists($existingLetter->file)) {
            Storage::disk('public')->delete($existingLetter->file); // ลบไฟล์เก่า
        }

        $data['file_path'] = $this->file->store('announcements', 'public'); // อัปโหลดใหม่
    } elseif ($existingLetter) {
        $data['file_path'] = $existingLetter->file; // ใช้ path เดิม
    }
    // if ($this->file) {
    //     $data['file_path'] = $this->file->store('announcements', 'public');
    // }
    

    if ($this->announcementId) {
        $data['updated_by'] = Auth::id();
        $announcement = Announcement::find($this->announcementId);
        $announcement->update($data);
        $existingLetter = \App\Models\Announcement::find($this->announcementId);
    } else {
        $data['created_by'] = Auth::id();
        $announcement = Announcement::create($data);
        $existingLetter = null;
    }

   

    $announcement->agencies()->sync($this->selected_agencies);
     $this->reset([ 'announcement_number', 'announcement_name', 'announcement_date', 'file_path', 'selected_agencies']);
       
    $this->showModal = false;

   
       

        // 👉 Toast แจ้งผล
        $message = $this->announcementId ? 'แก้ไขสำเร็จ' : 'บันทึกสำเร็จ';
        $text = 'ข้อมูลถูก' . ($this->announcementId ? 'แก้ไข' : 'บันทึก') . 'เรียบร้อยแล้ว';

        $this->js(<<<JS
            window.dispatchEvent(new CustomEvent('swal', {
                detail: {
                    icon: 'success',
                    title: '$message',
                    text: '$text',
                    showConfirmButton: false,
                    timerProgressBar: true,
                    position: 'top-end',
                    toast: true,
                    timer: 3000,
                }
            }))
        JS);
}

}
