<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\ReceivingAgency;
use Illuminate\Support\Facades\Hash;

class ManageUsers extends Component
{
//    public $selectedAgencies = [];
//     public $agencies = [];
//     public $users = [];

    public $users;
public $editing;
public $editingId;
public $newPassword;
public $receivingAgencies;
  public bool $showModal = false;
    public function mount()
    {
    //     $this->agencies = ReceivingAgency::all();
    //    $this->users = User::all();
       $this->users = User::with('receivingAgency')->where('role', '=', "user")->get();
       $this->receivingAgencies = ReceivingAgency::all();
      // $this->loadUsers();
    }
    public function edit($id)
    {
        $this->editingId = $id;

        $this->editing = User::findOrFail($id)->toArray();
        $this->editing['approved'] = (bool) $this->editing['approved']; // ✅ แปลงให้เป็น Boolean
        $this->newPassword = null;
           $this->showModal = true;


            //  $user = User::findOrFail($id)->toArray();
            // $user->approved = (bool) $user->approved; // แปลงชัดเจน
            // $this->editing = $user;
            // $this->newPassword = null;
            // $this->showModal = true;
      
    }
    public function updateUser()
    {
        $user = User::findOrFail($this->editingId);
        $user->name = $this->editing['name'];
        $user->email = $this->editing['email'];
        $user->receiving_agency_id = $this->editing['receiving_agency_id'] ?? null;
        $user->approved = $this->editing['approved'] ?? false;

        if ($this->newPassword) {
            $user->password = Hash::make($this->newPassword);
        }

        $user->save();
        
     
   
        $this->js(<<<JS
                window.dispatchEvent(new CustomEvent('swal', {
                    detail: {
                        icon: 'success',
                        title: 'บันทึกข้อมูลสำเร็จ',
                       // text: 'ข้อมูลถูกบันทึกเรียบร้อยแล้ว',
                        showConfirmButton: false,
                        timerProgressBar: true,
                        position: 'top-end',
                        toast: true,
                        timer: 3000,
                    }
                }))
            JS);
              $this->showModal = false; // ✅ ปิด Modal
             $this->loadUsers();
    }
     protected $listeners = ['confirm-delete' => 'delete'];
    public function delete($id)
    {
      
        User::findOrFail($id)->delete();
        
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
        $this->loadUsers();
    }
    public function loadUsers()
    {
        $this->users = User::with('receivingAgency')->where('role', '=', "user")->get();
    }
    public function approve($userId)
    {
        $user = User::findOrFail($userId);
        $user->approved = true;
        $user->save();
       // session()->flash('swal', ['icon' => 'success', 'title' => 'อนุมัติแล้ว']);
        $this->js(<<<JS
                window.dispatchEvent(new CustomEvent('swal', {
                    detail: {
                        icon: 'success',
                        title: 'อนุมัติแล้ว',
                       // text: 'ข้อมูลถูกบันทึกเรียบร้อยแล้ว',
                        showConfirmButton: false,
                        timerProgressBar: true,
                        position: 'top-end',
                        toast: true,
                        timer: 3000,
                    }
                }))
            JS);
             $this->loadUsers();
       
    }

    public function assignAgency($userId)
    {
        $user = User::findOrFail($userId);
        $agencyId = $this->selectedAgencies[$userId] ?? null;
            if (!empty($agencyId)) {
        $user->receiving_agency_id = $agencyId;
        $user->save();
        //session()->flash('swal', ['icon' => 'success', 'title' => 'อัปเดตแผนกเรียบร้อย']);
          $this->js(<<<JS
                window.dispatchEvent(new CustomEvent('swal', {
                    detail: {
                        icon: 'success',
                        title: 'อัปเดตแผนกเรียบร้อย',
                       // text: 'ข้อมูลถูกบันทึกเรียบร้อยแล้ว',
                        showConfirmButton: false,
                        timerProgressBar: true,
                        position: 'top-end',
                        toast: true,
                        timer: 3000,
                    }
                }))
            JS);
             $this->loadUsers();
    }
    }
}
