<div class="p-6 bg-white rounded-2xl shadow-md space-y-6">
   
    <div class="flex justify-between mb-2">
      
        <h1 class="text-2xl font-semibold text-gray-800 mb-6">รายการบันทึกข้อความ</h1>
            
           <flux:button size="sm" wire:click="create">+ เพิ่มบันทึกข้อความ</flux:button>
            
           
    
    </div>
    <div class="flex flex-wrap gap-4 items-end mb-4">
    <div>
        <label>ค้นหา</label>
        <input type="text" wire:model.defer="searchTerm" placeholder="เลขที่,เรื่อง"
            class="border px-3 py-2 rounded w-full" />
    </div>

    <div>
        <label>วันที่</label>
        <input type="date" wire:model.defer="searchDate"
            class="border px-3 py-2 rounded w-full" />
    </div>

    <div class="text-sm w-48">
        <label>จาก (เจ้าหน้าที่)</label>
        <select wire:model.defer="searchFromUser" class="border px-3 py-2 rounded w-full">
            <option value="">-- ทั้งหมด --</option>
            @foreach ($users as $user)
                <option value="{{ $user->id }}">{{ $user->name }}</option>
            @endforeach
        </select>
    </div>
    @if (auth()->user()->role === 'admin')
    <div class="text-sm w-48">
    <label >หน่วยงานผู้บันทึก</label>
    <select wire:model.defer="searchCreatedByAgency"
        class="border px-3 py-2 rounded w-full">
        <option value="">-- เลือกทั้งหมด --</option>
        @foreach ($receivingAgencies as $agency)
            <option value="{{ $agency->id }}">{{ $agency->name }}</option>
        @endforeach
    </select>
    </div>
    @endif

            <div>
                <label>&nbsp;</label>
                 <flux:button  wire:click="$refresh"  variant="primary" ><flux:icon.magnifying-glass variant="solid" /></flux:button>
            </div>
</div>


<table class="table-auto w-full border border-gray-200 text-sm">
    <thead class="bg-gray-100 text-left">
        <tr>
            <th class="px-4 py-2">เลขที่</th>
            <th class="px-4 py-2">เรื่อง</th>
            <th class="px-4 py-2">วันที่</th>
            <th class="px-4 py-2">เรียน</th>
            <th class="px-4 py-2">จาก</th>
            <th class="px-4 py-2">ไฟล์</th>
            <th class="px-4 py-2">จัดการ</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($memos as $memo)
        <tr class="border-t">
            <td class="px-4 py-2">{{ $memo->memo_number }}</td>
            <td class="px-4 py-2">{{ $memo->subject }}</td>
            <td class="px-4 py-2">{{ $memo->memo_date }}</td>
            <td class="px-4 py-2">{{ $memo->executive->name ?? '-' }}</td>
            <td class="px-4 py-2">{{ $memo->fromUser->name ?? '-' }}</td>
            <td class="px-4 py-2">
                @if($memo->file_path)
                <a href="{{ Storage::url($memo->file_path) }}" target="_blank">ดูไฟล์</a>
                @endif
            </td>
            <td class="px-4 py-2">
                {{-- <flux:button wire:click="edit({{ $memo->id }})">แก้ไข</flux:button>
                <flux:button wire:click="delete({{ $memo->id }})" variant="danger">ลบ</flux:button> --}}
                 <flux:button size="sm" wire:click="edit({{ $memo->id }})">แก้ไข</flux:button>
                <flux:button size="sm"
                                onclick="confirmDelete({{ $memo->id }})" 
                                variant="danger">ลบ</flux:button>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

{{-- {{ $memos->links() }} --}}
<div class="mt-4">
             <p>รวมทั้งหมด {{ $memos->total() }} รายการ / แสดงหน้า 
                {{ $memos->currentPage() }} จาก {{ $memos->lastPage() }}</p>
                <div class="flex justify-center mt-4 space-x-2">
    {{-- ก่อนหน้า --}}
                @if ($memos->onFirstPage())
                    <flux:button size="sm" disabled>ก่อนหน้า</flux:button>
                @else
                    <flux:button size="sm" wire:click="previousPage" wire:loading.attr="disabled">ก่อนหน้า</flux:button>
                @endif 

                {{-- ตัวเลขหน้า --}}
                 @foreach ($memos->getUrlRange(1, $memos->lastPage()) as $page => $url)
                    @if ($page == $memos->currentPage())
                        <flux:button size="sm" variant="danger" disabled>{{ $page }}</flux:button>
                    @else
                        <flux:button size="sm" wire:click="gotoPage({{ $page }})" wire:loading.attr="disabled">{{ $page }}</flux:button>
                    @endif
                @endforeach

                {{-- ถัดไป --}}
                @if ($memos->hasMorePages())
                    <flux:button size="sm" wire:click="nextPage" wire:loading.attr="disabled">ถัดไป</flux:button>
                @else
                    <flux:button size="sm" disabled>ถัดไป</flux:button>
                @endif
            </div>

            </div>
<flux:modal wire:model.defer="showModal" class="md:w-960">
         <h2 class="text-2xl font-bold mb-4">
                {{ $memoId ? 'แก้ไข' : 'เพิ่ม' }} ประกาศ
        </h2>
    <form wire:submit.prevent="save"  enctype="multipart/form-data">
            <div class="bg-white rounded p-6 w-full max-w-xl">
           
        <flux:input label="เลขที่" wire:model.defer="memo_number" />
        <flux:input type="date" label="วันที่" wire:model.defer="memo_date" />
        <flux:input label="เรื่อง" wire:model.defer="subject" />

        <flux:select label="เรียน" wire:model.defer="executive_id">
            @foreach($executives as $ex)
                <option value="{{ $ex->id }}">{{ $ex->name }}</option>
            @endforeach
        </flux:select>

        <flux:select label="จากเจ้าหน้าที่" wire:model.defer="from_user_id">
            @foreach($users as $u)
                <option value="{{ $u->id }}">{{ $u->name }}</option>
            @endforeach
        </flux:select>

        <flux:input type="file" label="ไฟล์แนบ" wire:model="file" />
        @error('file_path')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror

                @if ($memoId && $filePreviewPath = optional(\App\Models\Memo::find($memoId))->file_path)
                    <div class="mt-2">
                        <a href="{{ asset('storage/' . $filePreviewPath) }}" target="_blank" class="text-blue-600 underline">
                            ดูไฟล์เดิม
                        </a>
                    </div>
                @endif
       
    

                <div class="flex justify-end gap-2">
                    {{-- <button wire:click="$set('showModal', false)" class="px-4 py-2 bg-gray-300 rounded">ยกเลิก</button> --}}
                    <button wire:click="save" class="px-4 py-2 bg-green-500 text-white rounded">บันทึก</button>
                </div>
            </div>

            </form>
        </flux:modal>

</div>
