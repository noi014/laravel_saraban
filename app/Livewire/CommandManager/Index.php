<?php

namespace App\Livewire\CommandManager;

use Livewire\Component;
use App\Models\Command;
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
    //     return view('livewire.command-manager.index');
    // }
    public $command_number, $command_name, $command_date, $file_path, $letterId, $agency_ids = [];
    public $searchTerm, $searchDate,$departments , $showModal = false, $commandId;
   public $receivingAgencyOptions = [];
   public $searchCreatedByAgency = null;
    protected $listeners = ['confirm-delete' => 'delete'];
     public function create()
    {  
        $this->reset(['command_number', 'command_name', 'agency_ids', 'command_date', 'file_path', 'letterId']);
               
        $this->command_date = \Carbon\Carbon::now()->format('Y-m-d');
            // เคลียร์ validation error ต่าง ๆ
        $this->resetErrorBag();
        $this->resetValidation();
        $this->showModal = true;
    }
     public function mount()
    {
        if (Auth::user()->role === 'admin'|| Auth::user()->receiving_agency_id === 1) {
        $this->receivingAgencyOptions = \App\Models\ReceivingAgency::orderBy('name')->get();
        }
        $this->departments = ReceivingAgency::all();
    }

    public function render()
    {

     $query = Command::with('agencies');

     // ถ้าไม่ใช่ admin ให้ filter หน่วยงานของตัวเอง
    if (//Auth::user()->role !== 'admin'||
    Auth::user()->receiving_agency_id !== 1) {
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
        $query->where('command_number', 'like', '%' . $this->searchTerm . '%')
                ->orWhere('command_name', 'like', '%' . $this->searchTerm . '%');
    }

    if ($this->searchDate) {
        $query->whereDate('command_date', $this->searchDate);
    }

    
    $commands = $query->latest()->paginate(5); // ✅ โหลดใน render เท่านั้น

    return view('livewire.command-manager.index' , [
        'commands' => $commands,
    ]);

        
    }

    public function save()
    {
        $this->validate([
            'command_number' => 'required',
            'command_name' => 'required',
            'command_date' => 'required|date',
            'agency_ids' => 'required|array|min:1',
            'file_path' => 'nullable|file|max:20480',
        ]);

        $data = [
            'command_number' => $this->command_number,
            'command_name' => $this->command_name,
            'command_date' => $this->command_date,
            'updated_by' => Auth::id()
        ];

        // if (!$this->commandId) {
        //     $data['created_by'] = Auth::id();
        // }

        // if (is_object($this->file_path)) {
        //     $data['file_path'] = $this->file_path->store('commands', 'public');
        // }
    if ($this->letterId) {
        $data['updated_by'] = Auth::id();
        $existingLetter = \App\Models\Command::find($this->letterId);
    } else {
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();
        $existingLetter = null;
    }

    // 👉 ตรวจสอบและอัปโหลดไฟล์ใหม่
    if ($this->file_path instanceof UploadedFile) {
        if ($existingLetter && $existingLetter->file_path && Storage::disk('public')->exists($existingLetter->file_path)) {
            Storage::disk('public')->delete($existingLetter->file_path); // ลบไฟล์เก่า
        }

        $data['file_path'] = $this->file_path->store('commands', 'public'); // อัปโหลดใหม่
    } elseif ($existingLetter) {
        $data['file_path'] = $existingLetter->file_path; // ใช้ path เดิม
    }

        $command = Command::updateOrCreate(['id' => $this->commandId], $data);
        $command->agencies()->sync($this->agency_ids);

        $this->reset(['command_number', 'command_name', 'command_date', 'file_path', 'agency_ids', 'commandId']);
        $this->showModal = false;

        // 👉 Toast แจ้งผล
        $message = $this->letterId ? 'แก้ไขสำเร็จ' : 'บันทึกสำเร็จ';
        $text = 'ข้อมูลถูก' . ($this->letterId ? 'แก้ไข' : 'บันทึก') . 'เรียบร้อยแล้ว';

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

    public function edit($id)
    {
        $cmd = Command::with('agencies')->findOrFail($id);
        $this->commandId = $cmd->id;
        $this->command_number = $cmd->command_number;
        $this->command_name = $cmd->command_name;
        $this->command_date = $cmd->command_date;
        $this->agency_ids = $cmd->agencies->pluck('id')->toArray();
         $this->letterId = $id;
          // เคลียร์ validation error ต่าง ๆ
        $this->resetErrorBag();
        $this->resetValidation();
        $this->showModal = true;
    }

    // public function confirmDelete($id)
    // {
    //     Command::findOrFail($id)->delete();
    // }
    public function delete($id) 
    {
        $letter = Command::findOrFail($id);
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
}
