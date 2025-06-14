

<div class="w-full">

    <x-message></x-message>

<h2 class="text-2xl font-bold mb-4">
    {{ optional($editing)->id ? 'แก้ไขข้อมูลหนังสือรับราชการ' : 'เพิ่มข้อมูลหนังสือรับราชการ' }}
</h2>
<form wire:submit.prevent="save" class="space-y-2">
   
    <flux:input wire:model="reg_number" type="text" placeholder="เลขที่ลงรับ" class="input input-bordered" />
    <flux:input wire:model="reg_date" type="date" class="input input-bordered" placeholder="วันที่ลงรับ" label="วันที่ลงรับ"/>
    
    <flux:input wire:model="doc_number" type="text" placeholder="เลขที่หนังสือ" class="input input-bordered" />
    <flux:input wire:model="doc_date" type="date" class="input input-bordered" placeholder="วันที่หนังสือ" label="วันที่หนังสือ"/>





   

   
<!-- TailwindCSS + Livewire v3 + HTML datalist based autocomplete -->
<div>

<input 
    list="departments" 
    wire:model.lazy="from_agency_name" 
    wire:change="updateFromAgencyId" 
    placeholder="พิมพ์ชื่อหนังสือมาจากหน่วยงาน..."
    class="border px-3 py-2 w-full rounded 
           @error('from_agency_name') border-red-500 @else border-gray-300 @enderror"
>

    <datalist id="departments">
        @foreach ($departments as $department)
            <option value="{{ $department->name }}">
        @endforeach
    </datalist>

    <input type="hidden" wire:model="from_agency" />
   
</div>



  {{-- <flux:select wire:model="from_agency"  placeholder="หนังสือมาจากหน่วยงาน...">
        @foreach ($departments as $option)
        <flux:select.option value="{{ $option->id }}">
            {{ $option->name }}
        </flux:select.option>
        @endforeach
    </flux:select> --}}
 

  
    @if(Auth::user()->isAdmin())
    {{-- เฉพาะ admin --}}
    <div class="mt-2">
        <flux:button size="sm" wire:click="openDepartmentModal">+ เพิ่ม ชื่อหนังสือมาจากหน่วยงาน</flux:button>
    </div>
    
    @endif

    {{-- <flux:input wire:model="from_agency" type="text" placeholder="จากหน่วยงาน" class="input input-bordered" /> --}}
    {{-- <flux:input wire:model="to_agency" type="text" placeholder="ถึงหน่วยงาน" class="input input-bordered" /> --}}
  
    {{-- <flux:select wire:model="to_agency" placeholder="หนังสือส่งถึง...">
            @foreach ($receivingAgencies as $option)
            <flux:select.option value="{{ $option->id }}">
                {{ $option->name }}
            </flux:select.option>
            @endforeach
    </flux:select> --}}
    {{-- <flux:checkbox.group 
   class="flex gap-4 *:gap-x-8"
     wire:model="to_agency" 
     label="เลือกหน่วยงานที่รับหนังสือ">
       
        @foreach ($receivingAgencies as $option)
        <flux:checkbox  value="{{ $option->id }}" label="{{ $option->name }}" />
        @endforeach
    </flux:checkbox.group> --}}

    <flux:checkbox.group 
            class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2"
            wire:model="to_agency" 
            label="เลือกหน่วยงานที่รับหนังสือ">

            @foreach ($receivingAgencies->sortBy('id') as $option)
                <flux:checkbox value="{{ $option->id }}" label="{{ $option->name }}" />
            @endforeach
    </flux:checkbox.group>

    {{-- <flux:checkbox.group
    label="เลือกหน่วยงานที่รับหนังสือ"
    wire:model="selectedAgencyIds"
    :options="$receivingAgencies->pluck('name', 'id')->toArray()"
/> --}}
    @if(Auth::user()->isAdmin())
    {{-- เฉพาะ admin --}}
    <div class="mt-2">
        <flux:button size="sm" class="mt-2" wire:click="openReceivingAgencyModal">+ เพิ่มหน่วยงานที่รับหนังสือ</flux:button>
    </div>
    @endif
    <flux:input wire:model="subject" type="text" placeholder="ชื่อเรื่อง" class="input input-bordered col-span-2" />
    {{-- <flux:input wire:model="receiver_department" type="text" placeholder="หน่วยงานผู้รับ" class="input input-bordered col-span-2" /> --}}
    
     <div>
        {{-- อัปโหลดใหม่ --}}
        <flux:input type="file" wire:model="attachments" multiple label="แนบไฟล์ใหม่" />
    
        {{-- แสดงไฟล์เดิม (โหลดมาจาก $existingAttachments) --}}
        @if($existingAttachments)
            <div class="mt-2 text-sm text-gray-600">
                <strong>ไฟล์เดิม:</strong>
                <ul class="list-disc list-inside">
                    @foreach ($existingAttachments as $file)
                        <li>{{ basename($file['file_path']) }}</li>
                    @endforeach
                </ul>
                <p class="text-red-600 mt-1">* ไฟล์เดิมจะถูกลบเมื่ออัปโหลดใหม่</p>
            </div>
        @endif
    </div>
    <div class="col-span-2 flex gap-4">
        
        <flux:button variant="primary" type="submit"> 
            {{-- {{ $editId ? '💾 อัปเดตข้อมูล' : '💾 บันทึกข้อมูล' }} --}}
             {{ optional($editing)->id ? '💾 อัปเดตข้อมูล' : '💾 บันทึกข้อมูล' }}
        </flux:button>
       
         <flux:button  wire:click="$set('showModal', false)">ยกเลิก</flux:button>
        {{-- <flux:button href="{{ route('officialletter.index') }}">กลับ</flux:button> --}}
    </div>

    
</form>

<flux:modal wire:model="departmentModal" name="departmentModal" 
title="{{ $editingDepartment ? 'แก้ไขหน่วยงาน' : 'เพิ่มหน่วยงาน' }}"
>
    <flux:input wire:model.defer="department_name" label="ชื่อหน่วยงาน" />

    <div class="flex justify-end mt-4 space-x-2">
        <flux:button wire:click="$set('departmentModal', false)" variant="ghost">ยกเลิก</flux:button>
        <flux:button wire:click="saveDepartment" variant="primary">บันทึก</flux:button>
    </div>

    <div class="mt-6 border-t pt-4">
        <h4 class="font-bold mb-2">หน่วยงานทั้งหมด </h4>
        <ul class="space-y-1">
            @foreach($departments as $dept)
                <li class="flex justify-between items-center text-sm">
                    <span>{{ $dept->name }}</span>
                    <div class="space-x-2">
                        <button wire:click="openDepartmentModal({{ $dept->id }})" class="text-blue-500 text-xs">แก้ไข</button>
                        <button wire:click="deleteDepartment({{ $dept->id }})" class="text-red-500 text-xs">ลบ</button>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
    </flux:modal>
    <flux:modal wire:model="receivingAgencyModal" name="receivingAgencyModal" 
    title="{{ $editingReceivingAgency ? 'แก้ไขหน่วยงาน' : 'เพิ่มหน่วยงาน' }}"
    >
    <flux:input wire:model.defer="receivingAgencyName" label="ชื่อหน่วยงาน" />

    <div class="flex justify-end mt-4 space-x-2">
        <flux:button wire:click="$set('receivingAgencyModal', false)" variant="ghost">ยกเลิก</flux:button>
        <flux:button wire:click="saveReceivingAgency" variant="primary">บันทึก</flux:button>
    </div>

    <div class="mt-6 border-t pt-4">
        <h4 class="font-bold mb-2">หน่วยงานทั้งหมด</h4>
        <ul class="space-y-1">
            @foreach($receivingAgencies as $agency)
                <li class="flex justify-between items-center text-sm">
                    <span>{{ $agency->name }}</span>
                    <div class="space-x-2">
                        <button wire:click="openReceivingAgencyModal({{ $agency->id }})" class="text-blue-500 text-xs">แก้ไข</button>
                        <button wire:click="deleteReceivingAgency({{ $agency->id }})" class="text-red-500 text-xs">ลบ</button>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
</flux:modal>




</div>