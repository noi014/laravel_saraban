<?php

namespace App\Livewire\SendingLetter;

use Livewire\Component;
use App\Models\SendingLetter;
use Illuminate\Support\Facades\Auth;
use App\Models\Executive;
use App\Models\Department;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use App\Models\OutgoingLetter;
class Index extends Component
{
    use WithFileUploads;
    use WithPagination;
  

    protected $paginationTheme = 'tailwind'; // หากใช้ Tailwind
   // public $letters;
    public $file; // <<<<< เพิ่มตรงนี้
    public $doc_number, $doc_date, $subject, $sender_id, $file_path, $letterId, $agency_ids = [];
    public $sending_letter_id;
    public $showModal = false;
    public  $executives = [];
    public $departments;
    public $sendingLetter;
  
    public $selectedAgencyName;
    public $searchCreatedByAgency = null;
    protected $listeners = ['confirm-delete' => 'delete'];
    public function addAgency()
    {
        if (!$this->selectedAgencyName) return;

        $agency = Department::where('name', $this->selectedAgencyName)->first();

        if ($agency && !in_array($agency->id, $this->agency_ids)) {
            $this->agency_ids[] = $agency->id;
        }

        $this->selectedAgencyName = '';
    }

    public function removeAgency($id)
    {
        $this->agency_ids = array_filter($this->agency_ids, fn($i) => $i != $id);
    }
    public $receivingAgencyOptions = [];
    public function mount($id = null)
    {
        if (Auth::user()->role === 'admin') {
        $this->receivingAgencyOptions = \App\Models\ReceivingAgency::orderBy('name')->get();
        }
         $this->allDepartments = Department::all(); // หรือ ReceivingAgency::all();
        $this->departments = Department::all();
        
        $this->executives = Executive::all();
        
    }
    public function create()
    {  
        $this->reset(['doc_number', 'doc_date', 'agency_ids', 'subject', 'sender_id', 'file_path', 'letterId']);
        $this->sender_id = 1;             
        $this->doc_date = \Carbon\Carbon::now()->format('Y-m-d');
        $this->showModal = true;
    }
    public function edit($id)
    {
        // ดึงข้อมูลหนังสือส่ง
        $letter = SendingLetter::with('agencies')->findOrFail($id);

        // กรอกข้อมูลลงใน properties
        $this->fill($letter->toArray());

        // ดึง agency_ids จาก pivot table
        $this->agency_ids = $letter->agencies->pluck('id')->toArray();

        $this->letterId = $id;
        $this->departments = Department::all();
        
        $this->executives = Executive::all();
        // เคลียร์ validation error ต่าง ๆ
        $this->resetErrorBag();
        $this->resetValidation();

        // แสดง modal
        $this->showModal = true;
    }
    //  protected $rules = [
    //     'doc_number' => 'required',
    //     'doc_date' => 'required|date',
    //       'agency_ids' => 'required|array|min:1',
    //     'subject' => 'required',
    //     'sender_id' => 'required|exists:executives,id',
        
    //   'file_path' => 'nullable|file|mimes:pdf,docx,doc,jpg,jpeg,png|max:20480',
    // ];
//  public function save()
//     {
//         $this->validate();

//         $data = [
//             'doc_number' => $this->doc_number,
//             'doc_date' => $this->doc_date,
//             'agency_ids' => $this->agency_ids,
//             'subject' => $this->subject,
//             'sender_id' => $this->sender_id,
  
//         ];
//                 if ($this->letterId) {
//                             $data['updated_by'] = Auth::id();
                        
//                         } else {
//                             $data['created_by'] = Auth::id();
                        
//                         }
       
//         if ($this->file_path) {
//                 $path = $this->file_path->store('sending_letters', 'public');
//                 $data['file_path'] = $path;
//             }

//          $letter = SendingLetter::updateOrCreate(['id' => $this->letterId], $data);
//          $letter->agencies()->sync($this->agency_ids);

//         $this->showModal = false;
     
//          if (!empty($this->letterId)) {
//                     $this->js(<<<JS
//                 window.dispatchEvent(new CustomEvent('swal', {
//                     detail: {
//                         icon: 'success',
//                         title: 'แก้ไขสำเร็จ',
//                         text: 'ข้อมูลถูกแก้ไขเรียบร้อยแล้ว',
//                         showConfirmButton: false,
//                         timerProgressBar: true,
//                         position: 'top-end',
//                         toast: true,
//                         timer: 3000,
//                     }
//                 }))
//             JS);
//             }else{
//                 $this->js(<<<JS
//                 window.dispatchEvent(new CustomEvent('swal', {
//                     detail: {
//                         icon: 'success',
//                         title: 'บันทึกสำเร็จ',
//                         text: 'ข้อมูลถูกบันทึกเรียบร้อยแล้ว',
//                         showConfirmButton: false,
//                         timerProgressBar: true,
//                         position: 'top-end',
//                         toast: true,
//                         timer: 3000,
//                     }
//                 }))
//             JS);

//         }
//     }

    public function save()
{
    $rules = [
        'doc_number' => 'required',
        'doc_date' => 'required|date',
        'agency_ids' => 'required|array|min:1',
        'subject' => 'required',
        'sender_id' => 'required|exists:executives,id',
    ];

    // ถ้ามีไฟล์ใหม่และเป็น UploadedFile ให้เพิ่มกฎ validate
    if ($this->file_path instanceof UploadedFile) {
        $rules['file_path'] = 'nullable|file|mimes:pdf,docx,doc,jpg,jpeg,png|max:20480';
    }

    $this->validate($rules);

    $data = [
        'doc_number' => $this->doc_number,
        'doc_date' => $this->doc_date,
        'subject' => $this->subject,
        'sender_id' => $this->sender_id,
    ];

    if ($this->letterId) {
        $data['updated_by'] = Auth::id();
        $existingLetter = \App\Models\SendingLetter::find($this->letterId);
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

        $data['file_path'] = $this->file_path->store('sending_letters', 'public'); // อัปโหลดใหม่
    } elseif ($existingLetter) {
        $data['file_path'] = $existingLetter->file_path; // ใช้ path เดิม
    }

    // 👉 บันทึกข้อมูล
    $letter = \App\Models\SendingLetter::updateOrCreate(
        ['id' => $this->letterId],
        $data
    );

    $letter->agencies()->sync($this->agency_ids);

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

       
    

    public function search()
    {
        $this->resetPage(); // รีเซ็ต pagination
    }
    public $searchDepartments = []; // เก็บ id ของหน่วยงานที่เลือก
    public $allDepartments = [];

    public $keyword = '';
    public $textInput='';
    public $searchDate = null;
    public $searchAgency = '';
    // public function render()
    // {
        
       
    //      $query = SendingLetter::with('agencies', 'sender')
    //      ->join('users', 'sending_letters.created_by', '=', 'users.id')
    //      ;

    //    // 🔒 เงื่อนไขการเข้าถึง
    //         if (Auth::user()->role !== 'admin') {
    //             $query->where('users.receiving_agency_id', Auth::user()->receiving_agency_id);
    //         } else {
    //             // เฉพาะ admin: filter ตามหน่วยงานของผู้บันทึก
    //             if ($this->searchCreatedByAgency) {
    //                 $query->where('users.receiving_agency_id', $this->searchCreatedByAgency);
    //             }
    //         }
    //         // 🔍 เงื่อนไขการค้นหา
    //         $query->when($this->keyword, function ($q) {
    //             $q->where(function ($sub) {
    //                 $sub->where('sending_letters.doc_number', 'like', "%{$this->keyword}%")
    //                     ->orWhere('sending_letters.subject', 'like', "%{$this->keyword}%")
    //                     ->orWhere('departments.name', 'like', "%{$this->keyword}%");
    //             });
    //         });
    //          if ($this->searchDate) {
    //             $query->whereDate('sending_letters.doc_date', $this->searchDate);
    //         }

    //     $letters = $query->orderByDesc('sending_letters.id')->paginate(5);//$query->latest()->paginate(5);

    //     return view('livewire.sending-letter.index', [
    //         'letters' => $letters,
    //     ]);
    // }
   public function render()
{
    $query = SendingLetter::with('agencies', 'sender')->join('users', 'sending_letters.created_by', '=', 'users.id')
    ->select(
                    'sending_letters.*'
                );

    // ถ้าไม่ใช่ admin ให้แสดงเฉพาะที่ตัวเองบันทึก หรือหน่วยงานตัวเอง
    // if (Auth::user()->role !== 'admin') {
    //     // สมมุติว่า user มีฟิลด์ agency_id
    //     $userAgencyId = Auth::user()->receiving_agency_id;

    //     $query->where(function ($q) use ($userAgencyId) {
    //         // เงื่อนไขที่ 1: บันทึกโดย user นี้
    //         $q->where('created_by', Auth::id())

    //         // เงื่อนไขที่ 2: หรือ ส่งถึงหน่วยงานของ user นี้
    //         ->orWhereHas('agencies', function ($subQuery) use ($userAgencyId) {
    //             $subQuery->where('id', $userAgencyId);
    //         });
    //     });
    // }

    // กรองด้วยข้อความ
    if (!empty($this->textInput)) {
        $query->where(function ($q) {
            $q->where('doc_number', 'like', '%' . $this->textInput . '%')
              ->orWhere('subject', 'like', '%' . $this->textInput . '%')
              ->orWhereHas('agencies', function ($q2) {
                  $q2->where('name', 'like', '%' . $this->textInput . '%');
              });
        });
    }

    // วันที่
    if (!empty($this->searchDate)) {
        $query->whereDate('doc_date', $this->searchDate);
    }

    // เฉพาะ admin กรองตามหน่วยงานผู้บันทึก
    // if (!empty($this->searchCreatedByAgency)) {
    //     $query->where('created_by', $this->searchCreatedByAgency);
    // }
    // 🔒 เงื่อนไขการเข้าถึง
            if (Auth::user()->role !== 'admin') {
                $query->where('users.receiving_agency_id', Auth::user()->receiving_agency_id);
            } else {
                // เฉพาะ admin: filter ตามหน่วยงานของผู้บันทึก
                if ($this->searchCreatedByAgency) {
                    $query->where('users.receiving_agency_id', $this->searchCreatedByAgency);
                }
            }

    $letters = $query->latest()->paginate(5);

    return view('livewire.sending-letter.index', [
        'letters' => $letters,
    ]);
}





    public function delete($id) 
    {
        $letter = SendingLetter::findOrFail($id);
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
  

}
