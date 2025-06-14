<?php

// namespace App\Livewire\OutgoingLetter;

// use Livewire\Component;

// class Index extends Component
// {
//     public function render()
//     {
//         return view('livewire.outgoing-letter.index');
//     }
// }
namespace App\Livewire\OutgoingLetter;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\OutgoingLetter;
use App\Models\Department;
use App\Models\Executive;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;
use Illuminate\Http\UploadedFile;
class Index extends Component
{
    use WithFileUploads;
    use WithPagination;
    //public $search = '';
    // เพิ่มเพื่อ reset หน้าเมื่อค้นหา
    public $keyword = '';
    public $textInput='';
    public $searchDate = null;
    public $searchAgency = '';
   // protected $paginationTheme = 'tailwind'; // หรือใส่ fluxui ก็ได้ถ้ามี custom

    public $doc_number, $doc_date, $to_agency_id, $subject, $sender_id, $file, $letterId;
    public $showModal = false;
    public  $executives = [];//$department = [],
    public $to_agency_name = '';

    public $departments;
    public $departmentModal = false;
    public $editingDepartment = null;
    public $department_name = ''; 

    public $searchCreatedByAgency = null;
    public $receivingAgencyOptions = [];
    protected $listeners = ['confirm-delete' => 'delete'];

    protected $rules = [
        'doc_number' => 'required',
        'doc_date' => 'required|date',
       // 'to_agency_id' => 'required|exists:receiving_agencies,id',
       'to_agency_id' => 'required|exists:departments,id',
        'subject' => 'required',
        'sender_id' => 'required|exists:executives,id',
        'file' => 'nullable|file|mimes:pdf,docx,doc,jpg,jpeg,png|max:20480',
    ];
   public function updateToAgencyId()
    {
        $agency = \App\Models\Department::where('name', $this->to_agency_name)->first();
        
        if ($agency) {
            $this->to_agency_id = $agency->id;
        } else {
            $this->to_agency_id = null;
        }
    }
    public function updatedSearch()
    {
        $this->resetPage();
    }
    public function mount()
    {
        if (Auth::user()->role === 'admin') {
        $this->receivingAgencyOptions = \App\Models\ReceivingAgency::orderBy('name')->get();
        }
        $this->departments = Department::all();
        $this->executives = Executive::all();
    }

    public function create()
    {
        $this->reset(['doc_number', 'doc_date', 'to_agency_id', 'subject', 'sender_id', 'file', 'letterId']);
        $this->sender_id = 1;    
        $this->to_agency_name = '';         
        $this->doc_date = \Carbon\Carbon::now()->format('Y-m-d');
        $this->resetErrorBag(); // ✅ เคลียร์ validation error
        $this->resetValidation(); // ✅ เคลียร์ validation rule ที่ trigger ค้างอยู่
        $this->showModal = true;
    }

    public function edit($id)
    {
        // $letter = OutgoingLetter::findOrFail($id);
        // $this->fill($letter->toArray());
        // $this->letterId = $letter->id;
        // $this->showModal = true;

         $letter = OutgoingLetter::findOrFail($id);
        $this->fill($letter->toArray());
        $this->to_agency_name = $letter->toAgency->name ?? '';
        $this->letterId = $letter->id;
        $this->resetErrorBag(); // ✅ เคลียร์ validation error
        $this->resetValidation(); // ✅ เคลียร์ validation rule ที่ trigger ค้างอยู่
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'doc_number' => $this->doc_number,
            'doc_date' => $this->doc_date,
            'to_agency_id' => $this->to_agency_id,
            'subject' => $this->subject,
            'sender_id' => $this->sender_id,
            //'created_by' => Auth::id(),
            //'updated_by' => Auth::id(),
        ];

         if ($this->letterId) {
            $data['updated_by'] = Auth::id();
            $existingLetter = \App\Models\OutgoingLetter::find($this->letterId);
        } else {
            $data['created_by'] = Auth::id();
            $data['updated_by'] = Auth::id();
           $existingLetter = null;
        }
        // if ($this->file) {
        //     $data['file_path'] = $this->file->store('outgoing_letters', 'public');
        // }
        // 👉 ตรวจสอบและอัปโหลดไฟล์ใหม่
        if ($this->file instanceof UploadedFile) {
            if ($existingLetter && $existingLetter->file_path && Storage::disk('public')->exists($existingLetter->file_path)) {
                Storage::disk('public')->delete($existingLetter->file_path); // ลบไฟล์เก่า
            }

            $data['file_path'] = $this->file->store('outgoing_letters', 'public'); // อัปโหลดใหม่
        } elseif ($existingLetter) {
            $data['file_path'] = $existingLetter->file_path; // ใช้ path เดิม
        }

        OutgoingLetter::updateOrCreate(['id' => $this->letterId], $data);

        $this->showModal = false;
        //session()->flash('success', 'บันทึกข้อมูลเรียบร้อยแล้ว');
         if (!empty($this->letterId)) {
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
            }else{
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
    }

    public function delete($id)
    {
        $letter = OutgoingLetter::findOrFail($id);
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

    public function render()
    {
        // return view('livewire.outgoing-letter.index', [
        //     'letters' => OutgoingLetter::with('toAgency', 'sender')->orderBy('id', 'desc')->paginate(5)
        // ]);

                $query = OutgoingLetter::query()
                ->join('users', 'outgoing_letters.created_by', '=', 'users.id')
                ->join('departments', 'outgoing_letters.to_agency_id', '=', 'departments.id')
                ->leftJoin('executives', 'outgoing_letters.sender_id', '=', 'executives.id')
                ->select(
                    'outgoing_letters.*',
                    'departments.name as to_agency_name',
                    'executives.name as sender_name'
                );

        

            // 🔒 เงื่อนไขการเข้าถึง
            if (Auth::user()->role !== 'admin') {
                $query->where('users.receiving_agency_id', Auth::user()->receiving_agency_id);
            } else {
                // เฉพาะ admin: filter ตามหน่วยงานของผู้บันทึก
                if ($this->searchCreatedByAgency) {
                    $query->where('users.receiving_agency_id', $this->searchCreatedByAgency);
                }
            }

            // 🔍 เงื่อนไขการค้นหา
            $query->when($this->keyword, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('outgoing_letters.doc_number', 'like', "%{$this->keyword}%")
                        ->orWhere('outgoing_letters.subject', 'like', "%{$this->keyword}%")
                        ->orWhere('departments.name', 'like', "%{$this->keyword}%");
                });
            });

            if ($this->searchDate) {
                $query->whereDate('outgoing_letters.doc_date', $this->searchDate);
            }

            $letters = $query->orderByDesc('outgoing_letters.id')->paginate(5);

            return view('livewire.outgoing-letter.index', compact('letters'));
    }
     public function search(){
        $this->keyword=$this->textInput;
    }
}

