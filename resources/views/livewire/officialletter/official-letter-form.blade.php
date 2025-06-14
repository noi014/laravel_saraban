<div class="p-4">
    <div class="mb-4">
        <input wire:model="search" type="text" placeholder="ค้นหาด้วยชื่อเรื่อง..." class="input input-bordered w-full" />
    </div>
    <h2 class="text-2xl font-bold mb-4">{{ $editId ? 'แก้ไขหนังสือ' : 'เพิ่มหนังสือใหม่' }}</h2>
    <form wire:submit="save" enctype="multipart/form-data" class="space-y-2">
        <flux:input wire:model="form.ref_no" type="text" placeholder="เลขที่ลงรับ" class="input input-bordered" />
        <flux:input wire:model="form.ref_date" type="date" class="input input-bordered" />
        <flux:input wire:model="form.doc_no" type="text" placeholder="เลขที่หนังสือ" class="input input-bordered" />
        <flux:input wire:model="form.doc_date" type="date" class="input input-bordered" />
        <flux:input wire:model="form.from_agency" type="text" placeholder="หนังสือจากหน่วยงาน" class="input input-bordered" />
        <flux:input wire:model="form.to_agency" type="text" placeholder="หนังสือส่งถึง" class="input input-bordered" />
        <flux:input wire:model="form.subject" type="text" placeholder="ชื่อเรื่อง" class="input input-bordered col-span-2" />
        <flux:input wire:model="form.receiver_agency" type="text" placeholder="หน่วยงานที่รับหนังสือ" class="input input-bordered col-span-2" />
        <flux:input wire:model="file" type="file" class="file-input file-input-bordered col-span-2" multiple/>
        <div class="col-span-2 flex gap-4">
            
            <flux:button variant="primary" type="submit ">บันทึก</flux:button>
            
            <flux:button href="{{ route('letters.index') }}">กลับ</flux:button>
        </div>
    </form>

    <div class="divider">รายการหนังสือ</div>

    <table class="table w-full mt-4">
        <thead>
            <tr>
                <th>เลขที่ลงรับ</th>
                <th>ชื่อเรื่อง</th>
                <th>จากหน่วยงาน</th>
                <th>ถึง</th>
                <th>วันที่</th>
                <th>ไฟล์</th>
                <th>จัดการ</th>
            </tr>
        </thead>
        <tbody>
            @foreach($officialLetters as $letter)
            <tr>
                <td>{{ $letter->ref_no }}</td>
                <td>{{ $letter->subject }}</td>
                <td>{{ $letter->from_agency }}</td>
                <td>{{ $letter->to_agency }}</td>
                <td>{{ $letter->ref_date }}</td>
                <td>
                    @if($letter->file_path)
                        <a href="{{ Storage::url($letter->file_path) }}" class="link link-primary" target="_blank">เปิด</a>
                    @endif
                </td>
                <td>
                    <button wire:click="edit({{ $letter->id }})" class="btn btn-sm btn-warning">แก้ไข</button>
                    <button wire:click="delete({{ $letter->id }})" class="btn btn-sm btn-error">ลบ</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

