<?php

namespace App\Livewire\ItemRequest;

use Livewire\Component;
use App\Models\ItemRequest;
use App\Models\ItemRequestMessage;

class RequestMessages extends Component
{
    public ItemRequest $request;
    public string $message = '';

    protected $rules = [
        'message' => 'required|string|max:1000',
    ];

    public function send()
    {
        $this->validate();

        ItemRequestMessage::create([
            'item_request_id' => $this->request->id,
            'user_id' => auth()->id(),
            'message' => $this->message,
        ]);

        $this->message = '';
    }

    public function render()
    {
        return view('livewire.item-request.request-messages', [
            'messages' => $this->request->messages()->with('user')->get(),
        ]);
    }
}
