<?php

namespace App\Livewire\Admin;

use App\Models\Contact;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;

class ContactManager extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $selectedContact = null;
    public $showModal = false;
    public $adminNotes = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    #[Computed]
    public function contacts()
    {
        return Contact::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhere('subject', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function viewContact($contactId)
    {
        $this->selectedContact = Contact::findOrFail($contactId);
        $this->adminNotes = $this->selectedContact->admin_notes ?? '';
        $this->showModal = true;
    }

    public function updateStatus($contactId, $status)
    {
        $contact = Contact::findOrFail($contactId);
        $contact->update([
            'status' => $status,
            'responded_at' => $status === 'responded' ? now() : $contact->responded_at
        ]);

        $this->dispatch('contact-updated');
        session()->flash('success', 'Contact status updated successfully.');
    }

    public function saveNotes()
    {
        if ($this->selectedContact) {
            $this->selectedContact->update([
                'admin_notes' => $this->adminNotes
            ]);

            $this->dispatch('notes-saved');
            session()->flash('success', 'Notes saved successfully.');
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedContact = null;
        $this->adminNotes = '';
    }

    public function deleteContact($contactId)
    {
        Contact::findOrFail($contactId)->delete();
        
        $this->dispatch('contact-deleted');
        session()->flash('success', 'Contact deleted successfully.');
    }

    public function render()
    {
        return view('livewire.admin.contact-manager');
    }
}