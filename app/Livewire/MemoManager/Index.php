<?php

namespace App\Livewire\MemoManager;

use Livewire\Component;
use App\Models\Memo;
use App\Models\Executive;
use App\Models\User;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\UploadedFile;
class Index extends Component
{
    use WithFileUploads;
    use WithPagination;
    // public function render()
    // {
    //     return view('livewire.memo-manager.index');
    // }
     public $memo_number, $memo_date, $subject, $executive_id, $from_user_id, $file, $memoId;
    public $showModal = false;
protected $listeners = ['confirm-delete' => 'delete'];
public $searchTerm, $searchDate, $searchFromUser,$searchCreatedByAgency;
    public function search()
    {
        $this->resetPage(); // รีเซ็ต pagination
    }
    // public function render()
    // {
    //     $memos = Memo::with('executive', 'fromUser')->latest()->paginate(5);

    //     return view('livewire.memo-manager.index', [
    //         'memos' => $memos,
    //         'executives' => Executive::all(),
    //         'users' => User::where('receiving_agency_id', Auth::user()->receiving_agency_id)->get(),
    //     ]);
    // }
    public function render()
{
    $user = Auth::user();
    $userAgencyId = $user->receiving_agency_id;

    $query = Memo::with('executive', 'fromUser', 'creator');

    // ✅ จำกัดเฉพาะแผนกตัวเอง หากไม่ใช่ admin
    if ($user->role !== 'admin') {
        $query->whereHas('fromUser', function ($q) use ($userAgencyId) {
            $q->where('receiving_agency_id', $userAgencyId);
        });
    }

    // ✅ เงื่อนไขค้นหา
    if ($this->searchTerm) {
        $query->where(function ($q) {
            $q->where('memo_number', 'like', '%' . $this->searchTerm . '%')
              ->orWhere('subject', 'like', '%' . $this->searchTerm . '%');
        });
    }

    if ($this->searchDate) {
        $query->whereDate('memo_date', $this->searchDate);
    }

    if ($this->searchFromUser) {
        $query->where('from_user_id', $this->searchFromUser);
    }

    // ✅ เฉพาะ admin สามารถกรองจากหน่วยงานผู้บันทึก
    if ($user->role === 'admin' && $this->searchCreatedByAgency) {
        $query->whereHas('creator', function ($q) {
            $q->where('receiving_agency_id', $this->searchCreatedByAgency);
        });
    }

    $memos = $query->latest()->paginate(5);

    return view('livewire.memo-manager.index', [
        'memos' => $memos,
        'executives' => Executive::all(),
        'users' => User::where('receiving_agency_id', $userAgencyId)->get(),
        'receivingAgencies' => \App\Models\ReceivingAgency::all(), // 👈 โหลดทั้งหมดสำหรับ admin
    ]);
}


    public function create()
    {
        $this->reset();
                 
        $this->memo_date = \Carbon\Carbon::now()->format('Y-m-d');
        $this->executive_id = '1';
         $this->from_user_id = Auth::user()->id;
           // เคลียร์ validation error ต่าง ๆ
        $this->resetErrorBag();
        $this->resetValidation();
        $this->showModal = true;
    }

    public function edit($id)
    {
        $memo = Memo::findOrFail($id);
        $this->fill($memo->toArray());
        $this->memoId = $memo->id;
        // เคลียร์ validation error ต่าง ๆ
        $this->resetErrorBag();
        $this->resetValidation();
        $this->showModal = true;
    }

    public function save()
    {
        $data = $this->validate([
            'memo_number' => 'required',
            'memo_date' => 'required|date',
            'subject' => 'required',
            'executive_id' => 'required|exists:executives,id',
            'from_user_id' => 'required|exists:users,id',
            'file' => 'nullable|file|mimes:pdf,docx,jpg,png|max:20480',
        ]);
        if ($this->memoId) {
            $existingLetter = \App\Models\Memo::find($this->memoId);
        } else {
            $existingLetter = null;
        }
        // if ($this->file) {
        //     if ($this->memoId && $old = Memo::find($this->memoId)->file_path) {
        //         Storage::disk('public')->delete($old);
        //     }
        //     $data['file_path'] = $this->file->store('memos', 'public');
        // }
        // 👉 ตรวจสอบและอัปโหลดไฟล์ใหม่
        if ($this->file instanceof UploadedFile) {
            if ($existingLetter && $existingLetter->file && Storage::disk('public')->exists($existingLetter->file)) {
                Storage::disk('public')->delete($existingLetter->file); // ลบไฟล์เก่า
            }

            $data['file_path'] = $this->file->store('memos', 'public'); // อัปโหลดใหม่
        } elseif ($existingLetter) {
            $data['file_path'] = $existingLetter->file; // ใช้ path เดิม
        }

        $data['updated_by'] = Auth::id();
        if (!$this->memoId) {
            $data['created_by'] = Auth::id();
        }

        Memo::updateOrCreate(['id' => $this->memoId], $data);

        $this->reset([ 'memo_number', 'subject', 'memo_date', 'file', 'executive_id', 'from_user_id']);
        $this->showModal = false;
        // 👉 Toast แจ้งผล
        $message = $this->memoId ? 'แก้ไขสำเร็จ' : 'บันทึกสำเร็จ';
        $text = 'ข้อมูลถูก' . ($this->memoId ? 'แก้ไข' : 'บันทึก') . 'เรียบร้อยแล้ว';

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

    public function delete($id)
    {
        $memo = Memo::findOrFail($id);
        if ($memo->file_path) {
            Storage::disk('public')->delete($memo->file_path);
        }
        $memo->delete();
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
