
{{-- <container>
    <flux:input wire:model="search" placeholder="ค้นหา..."/>
    <button wire:click="$refresh">รีเฟรช</button>
    <table>
        <slot name="head">
            <th>เลขที่ลงรับ</th>
            <th>ชื่อเรื่อง</th>
            <th>วันที่</th>
            <th>Action</th>
        </slot>
        <slot name="body">
            @foreach($letters as $item)
                <tr>
                    <td>{{ $item->reg_number }}</td>
                    <td>{{ $item->subject }}</td>
                    <td>{{ $item->reg_date }}</td>
                    <td>
                        <a href="{{ route('officialletter.edit', $item->id) }}">✏️</a>
                        <button wire:click="delete({{ $item->id }})">🗑️</button>
                    </td>
                </tr>
            @endforeach
        </slot>
    </table>

    {{ $letters->links() }}
</container> --}}

<div class="w-full">
  
 @push('scripts')
<script>
    window.addEventListener('swal:success', event => {
        Swal.fire({
            icon: 'success',
            title: event.detail.title,
            text: event.detail.text,
            timer: 2000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end',
        })
    });
</script>
@endpush
        
    <div class="flex justify-between mb-2">
        {{-- <h1 class="text-3x1 font-bold">Letters</h1> --}}
        {{-- <div class="divider">รายการหนังสือ</div> --}}
        <h1 class="text-2xl font-semibold text-gray-800 mb-6">รายการหนังสือรับ</h1>
        {{-- <flux:button wire:navigate href="{{ route('officialletter.create') }}" variant="primary">+ เพิ่มหนังสือรับ</flux:button> --}}
            <flux:modal.trigger name="toggle-letter-modal">
                <flux:button size="sm" wire:click="create">+ เพิ่มหนังสือ</flux:button>
            </flux:modal.trigger>
    
    </div>
    <!-- Modal -->
        <flux:modal wire:model="showModal" title="{{ $editing?->id ? 'แก้ไขหนังสือ' : 'เพิ่มหนังสือ' }}"  class="md:w-960">
            @include('livewire.officialletter.form')

            {{-- <div class="flex justify-end mt-4 space-x-2">
                <flux:button variant="danger" wire:click="$set('showModal', false)">ยกเลิก</flux:button>
            
                <flux:button variant="primary" wire:click="save"> 
                        {{ $editing?->id ? '💾 อัปเดตข้อมูล' : '💾 บันทึกข้อมูล' }}
                </flux:button>
            </div> --}}
        </flux:modal>
    <div class="flex justify-between mb-4">
             <flux:input   wire:model="textInput" placeholder="ค้นหาด้วยชื่อเรื่อง / หนังสือมาจาก / เลขที่... / หน่วยงานที่รับหนังสือ" class="me-2" />
             <flux:button  wire:click="search" variant="primary">Search</flux:button>
    </div>
    <div class="grid auto-rows-min ">
        {{-- <x-message></x-message> --}}
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr class="border-b">
                         <td class="px-3 py-3">เลขที่</td>
                        <td class="px-3 py-3">เลขที่ลงรับ</td>
                        <td class="px-3 py-3">ชื่อเรื่อง</td>
                        <td class="px-3 py-3" >หนังสือมาจาก</td>
                        <td class="px-3 py-3" >หน่วยงานที่รับหนังสือ</td>
                        <td class="px-3 py-3">วันที่ลงรับ</td>
                        <td class="px-3 py-3" >จัดการ</td>
                    </tr>
                </thead>
                <tbody>
        
                    @foreach($letters as $letter)
                    <tr class="border-b">
                        <td class="px-3 py-3">{{ $letter->id }}</td>
                        <td class="px-3 py-3">{{ $letter->reg_number }}</td>
                        <td class="px-3 py-3">{{ $letter->subject }}</td>
                        <td class="px-3 py-3">
                          
                            {{ \App\Models\Department::find($letter->from_agency)?->name ?? 'ไม่พบข้อมูล' }}
                        </td>
                        {{-- <td class="px-3 py-3">
                            <ul class="list-disc list-inside">
                                @foreach($letter->to_agency as $agencyId)
                                    <li>{{ \App\Models\ReceivingAgency::find($agencyId)?->name ?? 'ไม่พบข้อมูล' }}</li>
                                @endforeach
                                
                            </ul>
                        </td> --}}
                        <td class="px-3 py-3">
                            <ul class="list-disc list-inside">
                                @php
                                    $rawToAgency = $letter->to_agency;
                                    $ids = is_string($rawToAgency) ? json_decode($rawToAgency, true) : (is_array($rawToAgency) ? $rawToAgency : []);
                                    $agencies = \App\Models\ReceivingAgency::whereIn('id', $ids)->pluck('name')->toArray();
                                @endphp
                               {{-- <li>{{ implode(', ', $agencies) }}</li> --}}
                                @forelse ($agencies as $agency)
                                    <li>{{ $agency }}</li>
                                @empty
                                    <li class="text-gray-400">ไม่มีข้อมูล</li>
                                @endforelse
                            </ul>
                        </td>
                        <td class="px-3 py-3">{{ $letter->reg_date }}</td>
                        <td class="px-3 py-3">
                            <flux:modal.trigger name="show-data">
                                <flux:button wire:click="show({{ $letter->id }})" size="sm">ดู</flux:button>
                            </flux:modal.trigger>
                             
                             
                            {{-- <flux:button wire:navigate href="{{ route('officialletter.edit', $letter->id) }}" size="sm"  variant="primary">แก้ไข</flux:button>
                             --}}
                             <flux:button size="sm" wire:click="edit({{ $letter->id }})">แก้ไข</flux:button>
                            <flux:button size="sm" 
                            wire:click="delete({{$letter->id}})"
                            wire:confirm="Are you sure you want to delete?"
                            
                            variant="danger">ลบ</flux:button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
       
          
          

 
            <div class="mt-4">
             <p>รวมทั้งหมด {{ $letters->total() }} รายการ / แสดงหน้า {{ $letters->currentPage() }} จาก {{ $letters->lastPage() }}</p>
             
               {{ $letters->links('pagination::simple-tailwind') }}
               
            </div>

            <flux:modal name="show-data" class="md:w-960">
                <div class="space-y-6">
                    <div>
                        <flux:heading size="lg">รายละเอียดหนังสือ</flux:heading>
                        {{-- <flux:text class="mt-2">Make changes to your personal details.</flux:text> --}}
                    </div>
                    @if($selectedLetter)
                    <div class="space-y-6">
                        <div class="col-span-2"><strong>เลขที่ลงรับ:</strong> {{ $selectedLetter->reg_number }}</div>
                        <div class="col-span-2"><strong>วันที่ลงรับ:</strong> {{ $selectedLetter->reg_date }}</div>
                        <div class="col-span-2"><strong>เลขที่หนังสือ:</strong> {{ $selectedLetter->doc_number }}</div>
                        <div class="col-span-2"><strong>วันที่หนังสือ:</strong> {{ $selectedLetter->doc_date }}</div>
                        <div class="col-span-2"><strong>จากหน่วยงาน:</strong>
                        {{ \App\Models\Department::find($selectedLetter->from_agency)?->name ?? 'ไม่พบข้อมูล' }}
                        </div>
                        <div class="col-span-2"><strong>หน่วยงานที่รับหนังสือ:</strong>
                            
                            <ul class="list-disc list-inside">
                                @foreach($selectedLetter->to_agency as $agencyId)
                                    <li>{{ \App\Models\ReceivingAgency::find($agencyId)?->name ?? 'ไม่พบข้อมูล' }}</li>
                                @endforeach
                            </ul>
                           
                        </div>
  
                        <div class="col-span-2"><strong>ชื่อเรื่อง:</strong> {{ $selectedLetter->subject }}</div>
                        <div class="col-span-2"><strong>หน่วยงานที่รับ:</strong> {{ $selectedLetter->receiver_department }}</div>
                        <div class="col-span-2">
                            <strong>ไฟล์แนบ:</strong>
                            <ul class="list-disc list-inside">
                                @foreach ($selectedLetter->attachments ?? [] as $file)
                                    <li>
                                        <a href="{{ asset('storage/'.$file->file_path) }}" target="_blank" class="text-blue-600 underline">
                                            {{ basename($file->file_path) }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
                </div>
            </flux:modal>


            
            
    </div>
  

    
</div>