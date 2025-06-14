<?php

// app/Livewire/Officialletter/Index.php

namespace App\Livewire\Officialletter;

use Livewire\Component;
use App\Models\OfficialLetter;
use Laravel\Pail\ValueObjects\Origin\Console;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Department;
use App\Models\ReceivingAgency;
use Livewire\WithFileUploads;
use App\Models\OfficialLetterAttachment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $letterId;
    public $reg_number, $reg_date, $doc_number, $doc_date,
         //  $from_agency,
            $to_agency, $subject, $receiver_department ,$created_by,$updated_by;
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
    public $showingModal = false;
    public $selectedLetter;
    protected $queryString =['keyword'];
    public $keyword='';
    public $textInput='';
    public $searchDate = null;
    public bool $showModal = false;
    public ?OfficialLetter $editing = null;
    protected $listeners = ['confirm-delete' => 'delete'];

    public function getFilteredLettersProperty()
    {
        $user = Auth::user();

        // admin เห็นทุกอย่าง
        if ($user->role === 'admin') {
            return OfficialLetter::latest()->get();
        }

        // สำนักปลัด เห็นทุกอย่าง
        if ($user->receiving_agency_id && ReceivingAgency::find($user->receiving_agency_id)?->name === 'สำนักปลัด') {
            return OfficialLetter::latest()->get();
        }

        // อื่น ๆ เห็นเฉพาะของแผนกตัวเอง
        return OfficialLetter::whereJsonContains('to_agency', $user->receiving_agency_id)->latest()->get();
    }
 
    public function delete($id)
    {
         $letter = OfficialLetter::with('attachments')->findOrFail($id);
        $letter->delete();
        // 🔥 ลบไฟล์จาก storage
        foreach ($letter->attachments as $attachment) {
            Storage::disk('public')->delete($attachment->file_path);
            $attachment->delete();
        }

        // 🗑️ ลบข้อมูลหลัก
       // $letter->delete();

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
   
    public function create()
    {
      
            $this->reset([
                'editing',
                'from_agency',
                'from_agency_name',
                'subject',
                'reg_number',
                'reg_date',
                'doc_number',
                'doc_date',
                'to_agency',
                'receivingAgencies', // ถ้ามีหลายหน่วยงาน
                'attachments',
            ]);
            $this->existingAttachments =[];
            $this->editing = new OfficialLetter(); // ตั้งค่าใหม่
            $this->letterId=null;
            $this->from_agency = Department::where('name', 'LIKE', '%สำนัก%')->value('id') 
                                    ?? $this->departments->first()?->id;
                                    $this->to_agency = [1]; 
                                    $this->reg_date = Carbon::now()->format('Y-m-d');
                $this->resetErrorBag(); // ✅ เคลียร์ validation error
            $this->resetValidation(); // ✅ เคลียร์ validation rule ที่ trigger ค้างอยู่
            $this->showModal = true;

            }

            public function edit(OfficialLetter $letter)
            {
            
                $this->resetErrorBag(); // ✅ เคลียร์ validation error
                $this->resetValidation(); // ✅ เคลียร์ validation rule ที่ trigger ค้างอยู่
                if ($letter->id) {
                            $this->officialLetter = OfficialLetter::findOrFail($letter->id);
                            $this->from_agency = $this->officialLetter->from_agency;
                            $this->from_agency_name = optional(Department::find($this->from_agency))->name;
                            $this->letterId = $this->officialLetter->id;
                            $this->fill($this->officialLetter->toArray());
                            $this->selectedAgencyIds = $this->officialLetter->to_agency ?? [];
                            $letter = OfficialLetter::with('attachments')->findOrFail($letter->id);
                            $this->editId = $letter->id;
                            $this->editing= $letter;
                            $this->attachments =null;
                            // โหลดไฟล์เดิม (แสดงในหน้า)
                            $this->existingAttachments = $letter->attachments->toArray();
                            // preload ข้อมูลฟอร์มอื่นๆ ด้วย
                            $this->reg_date = $letter->reg_date;
                }

                $this->showModal = true;
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
                                //'receiver_department' => 'required',
                                'attachments.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx,xlsx,xls|max:20480',
                            
                            ]);
                            if ($this->letterId) {
                            $data['updated_by'] = Auth::id();
                        
                        } else {
                            $data['created_by'] = Auth::id();
                        
                        }
                        // $this->to_agency = $this->selectedAgencyIds;
                            $letter = OfficialLetter::updateOrCreate(
                                ['id' => $this->letterId],
                                $data
                            );
                        
                            // สำคัญ: sync หลังจาก save แล้วเท่านั้น
                            if (!empty($this->letterId)) {
                                $this->officialLetter->receivingAgencies()->sync($this->letterId);
                            
                    //   session()->flash('swal', 
                    //         [
                    //             'icon'=> 'success',
                    //             'title'=> 'แก้ไขสำเร็จ',
                    //         'text'=> 'ข้อมูลถูกแก้ไขเรียบร้อยแล้ว',
                    //         'showConfirmButton'=> false,
                    //         'timerProgressBar'=> true,
                    //         'position'=>'top-end',
                    //         'toast'=> true,
                    //         'timer'=> 3000,
                    //         ]
                    //         );
                    $this->js(<<<JS
                        window.dispatchEvent(new CustomEvent('swal', {
                            detail: {
                                icon: 'success',
                                title: 'แก้ไขสำเร็จ',
                                text: 'ข้อมูลถูกแก้ไขเรียบร้อยแล้ว',
                                showConfirmButton: false,
                                timerProgressBar: true,
                                position: 'top-end',
                                toast: true,
                                timer: 3000,
                            }
                        }))
                    JS);

                    
                            } else {
                            //  $this->officialLetter->receivingAgencies()->detach(); // ถ้าไม่มี selections
                            if ($this->editing && $this->editing->id) {
                        $this->editing->receivingAgencies()->sync($this->selectedAgencies);
                                } 

                        
                        
                                $this->js(<<<JS
                        window.dispatchEvent(new CustomEvent('swal', {
                            detail: {
                                icon: 'success',
                                title: 'บันทึกสำเร็จ',
                                text: 'ข้อมูลถูกบันทึกเรียบร้อยแล้ว',
                                showConfirmButton: false,
                                timerProgressBar: true,
                                position: 'top-end',
                                toast: true,
                                timer: 3000,
                            }
                        }))
                    JS);
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
        // $this->validate([
        //     'editing.reg_number' => 'required',
        //     'editing.reg_date' => 'required|date',
        //     // เพิ่ม validation ตามต้องการ
        // ]);

        // $this->editing->save();
        $this->showModal = false;
        //session()->flash('success', 'บันทึกเรียบร้อยแล้ว');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
  
    public function render()
    {
       

        $this->departments = Department::orderBy('name')->get();

        $this->receivingAgencies = ReceivingAgency::orderBy('name')->get();

        $user =  Auth::user();
       // $isAdmin = $user->role === 'admin';
        $isAdmin = $user && $user->role === 'admin';
        // สำนักปลัด: ตรวจจากตาราง receiving_agencies เช่นชื่อ == สำนักปลัด
        $isSecretariat = ReceivingAgency::where('id', $user->receiving_agency_id)
                            ->where('name', 'like', '%สำนักปลัด%')
                            ->exists();

//  $letters = OfficialLetter::query()
//     ->join('departments', 'official_letters.from_agency', '=', 'departments.id')
//     ->leftJoin('receiving_agencies', function ($join) {
//         $join->on(DB::raw("1"), '=', DB::raw("1"))
//             ->whereRaw("official_letters.to_agency LIKE CONCAT('%\"', receiving_agencies.id, '\"%')");
//     })
//     ->select(
//         'official_letters.id',
//         'official_letters.from_agency',
//         'official_letters.to_agency', // ✅ ต้องมี
//         'official_letters.reg_number',
//         'official_letters.subject',
//         'official_letters.reg_date',
//        'official_letters.created_by',
//        'official_letters.updated_by',
//         'departments.name as from_agency_name'
//     )
//     ->when($this->keyword, function ($query) {
//         $query->where(function ($q) {
//             $q->where('official_letters.subject', 'like', "%{$this->keyword}%")
//               ->orWhere('official_letters.reg_number', 'like', "%{$this->keyword}%")
//               ->orWhere('departments.name', 'like', "%{$this->keyword}%")
//               ->orWhere('receiving_agencies.name', 'like', "%{$this->keyword}%");
//         });
//     })
//     ->groupBy(
//         'official_letters.id',
//         'official_letters.from_agency',
//         'official_letters.to_agency', // ✅ ใส่ให้ตรงกับ select
//         'official_letters.reg_number',
//         'official_letters.subject',
//         'official_letters.reg_date',
//         'official_letters.created_by',
//        'official_letters.updated_by',
//         'departments.name'
//     )
//     ->orderBy('official_letters.id', 'desc')
//     ->paginate(5);

    $letters = OfficialLetter::query()
    ->join('departments', 'official_letters.from_agency', '=', 'departments.id')
    ->leftJoin('receiving_agencies', function ($join) {
        $join->on(DB::raw("1"), '=', DB::raw("1"))
            ->whereRaw("official_letters.to_agency LIKE CONCAT('%\"', receiving_agencies.id, '\"%')");
    })
    ->select(
        'official_letters.id',
        'official_letters.from_agency',
        'official_letters.to_agency',
        'official_letters.reg_number',
        'official_letters.subject',
        'official_letters.reg_date',
        'official_letters.created_by',
        'official_letters.updated_by',
        'departments.name as from_agency_name'
    )
    ->when(!($isAdmin || $isSecretariat), function ($query) use ($user) {
        // จำกัดเฉพาะแผนกของตัวเอง (to_agency LIKE id)
        $query->whereRaw("official_letters.to_agency LIKE ?", ['%"' . $user->receiving_agency_id . '"%']);
    })
    ->when(($this->searchDate), function ($query)  {
       
        $query->whereDate('official_letters.reg_date', $this->searchDate);
    })
    ->when($this->keyword, function ($query) {
        $query->where(function ($q) {
            $q->where('official_letters.subject', 'like', "%{$this->keyword}%")
              ->orWhere('official_letters.reg_number', 'like', "%{$this->keyword}%")
              ->orWhere('departments.name', 'like', "%{$this->keyword}%")
              ->orWhere('receiving_agencies.name', 'like', "%{$this->keyword}%");
        });
    })
    ->groupBy(
        'official_letters.id',
        'official_letters.from_agency',
        'official_letters.to_agency',
        'official_letters.reg_number',
        'official_letters.subject',
        'official_letters.reg_date',
        'official_letters.created_by',
        'official_letters.updated_by',
        'departments.name'
    )
    ->orderBy('official_letters.id', 'desc')
    ->paginate(5);

    

        return view('livewire.officialletter.index', compact('letters'));
    }
    public function search(){
        $this->keyword=$this->textInput;
    }

    // public function render()
    // {
    //     $letters = OfficialLetter::where('reg_number', 'like', '%'.$this->search.'%')
    //         ->orWhere('doc_number', 'like', '%'.$this->search.'%')
    //         ->latest()
    //         ->paginate(10);

    //     return view('livewire.officialletter.index', compact('letters'));
    // }

    public function show($id)
    { 
      
        $this->selectedLetter = OfficialLetter::with('attachments')->findOrFail($id);
       $this->showingModal = false;
      
    
    }
  
    // public function delete($id)
    // {
    //     // $letter = OfficialLetter::findOrFail($id);
    //     // $letter->attachments()->delete();
    //     // $letter->delete();

    //     $letter = OfficialLetter::with('attachments')->findOrFail($id);

    //     // 🔥 ลบไฟล์จาก storage
    //     foreach ($letter->attachments as $attachment) {
    //         Storage::disk('public')->delete($attachment->file_path);
    //         $attachment->delete();
    //     }

    //     // 🗑️ ลบข้อมูลหลัก
    //     $letter->delete();

    // session()->flash('success', 'ลบข้อมูลเรียบร้อยแล้ว');
    // }
}
