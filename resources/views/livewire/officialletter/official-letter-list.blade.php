<div class="w-full">
    <div class="flex justify-between mb-2">
        <h1 class="text-3x1 font-bold">Letters</h1>
        <flux:button wire:navigate href="{{ route('letters.create') }}" variant="primary">+ เพิ่มหนังสือ</flux:button>
    </div>
    <div class="flex justify-between mb-4">
       
        <flux:input   wire:model="search" type="text" placeholder="ค้นหาชื่อเรื่อง..."  class="me-2" />
        <flux:button  wire:click="search" variant="primary">Search</flux:button>
    </div>
    <div class="w-full">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr class="border-b">
                        <td class="px-3 py-3">เลขที่ลงรับ</td>
                        <td class="px-3 py-3">ชื่อเรื่อง</td>
                        <td class="px-3 py-3" >จากหน่วยงาน</td>
                        <td class="px-3 py-3">วันที่</td>
                        <td class="px-3 py-3" >จัดการ</td>
                    </tr>
                </thead>
                <tbody>
                    @foreach($letters as $letter)
                    <tr class="border-b">
                        <td class="px-3 py-3">{{ $letter->ref_no }}</td>
                        <td class="px-3 py-3">{{ $letter->subject }}</td>
                        <td class="px-3 py-3">{{ $letter->from_agency }}</td>
                        <td class="px-3 py-3">{{ $letter->ref_date }}</td>
                        <td class="px-3 py-3">
                           
                            <flux:button wire:navigate href="{{ route('letters.edit', $letter->id) }}" size="sm"  variant="primary">แก้ไข</flux:button>
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
                {{ $letters->links() }}
            </div>
    
    </div>   
</div>
