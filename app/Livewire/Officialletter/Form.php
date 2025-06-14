<?php
// app/Livewire/Officialletter/Form.php

namespace App\Livewire\Officialletter;

use Livewire\Component;
use App\Models\OfficialLetter;
use App\Models\OfficialLetterAttachment;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use App\Models\Department;
use App\Models\ReceivingAgency;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Log;

class Form extends Component
{
    use WithFileUploads;

    public $letterId;
    public $reg_number, $reg_date, $doc_number, $doc_date,
         //  $from_agency,
            $to_agency, $subject, $receiver_department;

    public $attachments = [];
    public $existingAttachments = []; // ไฟล์เดิม
    public $editId = null;
    
  
    public $departments;
    public $departmentModal = false;
    public $editingDepartment = null;
    public $department_name = '';
    // เปิด modal เพิ่ม/แก้ไข


    public $receivingAgencyModal = false;
    public $editingReceivingAgency = null;
    public $receivingAgencyName = '';
   

   

    public $officialLetter;
    public $selectedAgencyIds = [];
    public $receivingAgencies = [];

   





   

public int|null $from_agency = null;


public string $from_agency_name = '';



    public function updateFromAgencyId()
    {
        // $department = Department::where('name', $this->from_agency_name)->first();
        // $this->from_agency = $department?->id;
        $match = Department::where('name', $this->from_agency_name)->first();
        $this->from_agency = $match?->id;
    }
    public function openDepartmentModal($id = null)
    {
        $this->reset(['editingDepartment', 'department_name']);
        if ($id) {
            $this->editingDepartment = Department::find($id);
            $this->department_name = $this->editingDepartment->name;
        }
        $this->departmentModal = true;  
    }

    // บันทึกหน่วยงาน
    public function saveDepartment()
    {
        $this->validate(['department_name' => 'required|string|max:255']);
        Department::updateOrCreate(
            ['id' => optional($this->editingDepartment)->id],
            ['name' => $this->department_name]
        );

        $this->departmentModal = false;
        $this->departments = Department::orderBy('name')->get();
        
      session()->flash('success', 'บันทึก ชื่อหนังสือมาจากหน่วยงาน เรียบร้อย');
       
    }

    // ลบหน่วยงาน
    public function deleteDepartment($id)
    {
        Department::findOrFail($id)->delete();
        $this->departments = Department::orderBy('name')->get();
        session()->flash('success', 'ลบหน่วยงานเรียบร้อย');
    }

    public function openReceivingAgencyModal($id = null)
    {
        $this->reset(['receivingAgencyName', 'editingReceivingAgency']);
        if ($id) {
            $this->editingReceivingAgency = ReceivingAgency::find($id);
            $this->receivingAgencyName = $this->editingReceivingAgency->name;
        }
        $this->receivingAgencyModal = true;
    }

    public function saveReceivingAgency()
    {
        $this->validate(['receivingAgencyName' => 'required|string|max:255']);
        ReceivingAgency::updateOrCreate(
            ['id' => optional($this->editingReceivingAgency)->id],
            ['name' => $this->receivingAgencyName]
        );  
       
        $this->receivingAgencyModal = false;
        $this->receivingAgencies = ReceivingAgency::orderBy('name')->get();
         session()->flash('success', 'บันทึก หน่วยงานที่รับหนังสือ เรียบร้อย');
    }

    public function deleteReceivingAgency($id)
    {
        ReceivingAgency::findOrFail($id)->delete();
        $this->receivingAgencies = ReceivingAgency::orderBy('name')->get();
    }
 
    
    public function mount($id = null)
    {
        // โหลดหน่วยงาน
       
     
        $this->departments = Department::orderBy('name')->get();

        $this->receivingAgencies = ReceivingAgency::orderBy('name')->get();
     

        // if ($id) {
        //       $letter = OfficialLetter::findOrFail($id);
        //       $this->letterId = $letter->id;
        //       $this->fill($letter->toArray());
        //     // $this->editId = $id;

        //     $letter = OfficialLetter::with('attachments')->findOrFail($id);
        //     $this->editId = $letter->id;
        //     // โหลดไฟล์เดิม (แสดงในหน้า)
        //     $this->existingAttachments = $letter->attachments->toArray();
        //     // preload ข้อมูลฟอร์มอื่นๆ ด้วย
        // }
        if ($id) {

        

            $this->officialLetter = OfficialLetter::findOrFail($id);

                $this->from_agency = $this->officialLetter->from_agency;
        $this->from_agency_name = optional(Department::find($this->from_agency))->name;

                    $this->letterId = $this->officialLetter->id;
                    $this->fill($this->officialLetter->toArray());
            
                    $this->selectedAgencyIds = $this->officialLetter->to_agency ?? [];

                    $letter = OfficialLetter::with('attachments')->findOrFail($id);
                    $this->editId = $letter->id;
            // โหลดไฟล์เดิม (แสดงในหน้า)
                    $this->existingAttachments = $letter->attachments->toArray();
            // preload ข้อมูลฟอร์มอื่นๆ ด้วย
             $this->reg_date = $letter->reg_date;
        } else {
             $this->from_agency = Department::where('name', 'LIKE', '%สำนัก%')->value('id') 
                             ?? $this->departments->first()?->id;
                             $this->to_agency = [1]; 
                              $this->reg_date = Carbon::now()->format('Y-m-d');
            $this->officialLetter = new OfficialLetter();
        }

    }

    public function save()
    {
        $data = $this->validate([
            'reg_number' => 'required',
            'reg_date' => 'required|date',
            'doc_number' => 'required',
            'doc_date' => 'required|date',
            'from_agency' => 'required',
              'from_agency_name' => 'required',
           'to_agency' => 'required',
            'subject' => 'required',
           // 'receiver_department' => 'required',
            'attachments.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx,xlsx,xls|max:20480',
          
        ]);
       // $this->to_agency = $this->selectedAgencyIds;
   
        $letter = OfficialLetter::updateOrCreate(
            ['id' => $this->letterId],
            $data
        );
     
        // สำคัญ: sync หลังจาก save แล้วเท่านั้น
        if (!empty($this->letterId)) {
            $this->officialLetter->receivingAgencies()->sync($this->letterId);

  session()->flash('swal', 
        [
            'icon'=> 'success',
            'title'=> 'แก้ไขสำเร็จ',
        'text'=> 'ข้อมูลถูกแก้ไขเรียบร้อยแล้ว',
        'showConfirmButton'=> false,
        'timerProgressBar'=> true,
        'position'=>'top-end',
        'toast'=> true,
        'timer'=> 3000,
        ]
        );
 
        } else {
            $this->officialLetter->receivingAgencies()->detach(); // ถ้าไม่มี selections
         

     session()->flash('swal', 
        [
            'icon'=> 'success',
            'title'=> 'บันทึกสำเร็จ',
        'text'=> 'ข้อมูลถูกบันทึกเรียบร้อยแล้ว',
        'showConfirmButton'=> false,
        'timerProgressBar'=> true,
        'position'=>'top-end',
        'toast'=> true,
        'timer'=> 3000,
        ]
        );
        }

        // foreach ($this->attachments as $file) {
        //     $path = $file->store('officialletters', 'public');
        //     OfficialLetterAttachment::create([
        //         'official_letter_id' => $letter->id,
        //         'file_path' => $path
        //     ]);
        // }

        // ✅ ลบไฟล์เดิมออก
        // if ($this->letterId) {
        //     foreach ($letter->attachments as $oldFile) {
        //         Storage::delete($oldFile->file_path); // ลบไฟล์จริง
        //         $oldFile->delete(); // ลบ DB
        //     }
        // }

        // ลบไฟล์แนบเก่าออกจาก storage และ database
        if (!empty($this->attachments)) {
       
            foreach ($letter->attachments as $oldFile) {
                Storage::disk('public')->delete($oldFile->file_path);
                $oldFile->delete();
            }
       

        // ✅ อัปโหลดไฟล์ใหม่
        foreach ($this->attachments as $uploadedFile) {
            $path = $uploadedFile->store('officialletters', 'public');
                OfficialLetterAttachment::create([
                'official_letter_id' => $letter->id,
                'file_path' => $path
            ]);
        }
    }

        // session()->flash('success', 'บันทึกเรียบร้อยแล้ว++++');
   
      
        return redirect()->route('officialletter.index');
    }


    public function render()
    {
        

        return view('livewire.officialletter.form');
    //     return view('livewire.officialletter.form'
    // , [
    //         'departments' => Department::where('name', 'like', '%' . $this->search . '%')->get(),
    //     ]
    // );
    }
}
