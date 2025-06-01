<?php

namespace App\Livewire;

use App\Models\Contact;
use Livewire\Component;
use Livewire\Attributes\Validate;

class ContactForm extends Component
{
    #[Validate('required|string|min:2|max:255')]
    public $name = '';

    #[Validate('required|email|max:255')]
    public $email = '';

    #[Validate('nullable|string|max:20')]
    public $phone = '';

    #[Validate('required|string|min:3|max:255')]
    public $subject = '';

    #[Validate('required|string|min:10|max:2000')]
    public $message = '';

    public $showSuccessMessage = false;

    public function submit()
    {
        $this->validate();

        try {
            Contact::create([
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'subject' => $this->subject,
                'message' => $this->message,
                'status' => 'pending'
            ]);

            $this->reset(['name', 'email', 'phone', 'subject', 'message']);
            $this->showSuccessMessage = true;

            // hide success message after 5 seconds
            $this->dispatch('contact-submitted');

        } catch (\Exception $e) {
            $this->addError('form', 'There was an error submitting your message. Please try again.');
        }
    }

    public function hideSuccessMessage()
    {
        $this->showSuccessMessage = false;
    }

    public function render()
    {
        return view('livewire.contact-form');
    }
}