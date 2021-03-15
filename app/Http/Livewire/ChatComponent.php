<?php

namespace App\Http\Livewire;

use App\Models\Message;
use Illuminate\Support\Facades\Cookie;
use Livewire\Component;

class ChatComponent extends Component
{
    public $roomId;
    public $message;
    public $chatPopupVisibility;

    /**
     * The mount function
     *
     * @return void
     */
    public function mount()
    {
        // Static values
        if (in_array(auth()->user()->id, [1, 2])) { // Room for Billy and Adrian
            $this->roomId = 1;
        } else { // Room for Richard and John
            $this->roomId = 2;
        }

        // Sets the initial state of the chat popup during page load or reload
        $this->chatPopupVisibility = Cookie::get('chatPopupShow') == 'true' ? true : false;
    }

    /**
     * Shows the chat popup box
     *
     * @return void
     */
    public function showChatPopup()
    {
        Cookie::queue('chatPopupShow', 'true', 60);
        $this->chatPopupVisibility = true;

        // load chat history by reloading the page
        $this->dispatchBrowserEvent('reload-page');
    }

    /**
     * Hides the chat popup box
     *
     * @return void
     */
    public function closeChatPopup()
    {
        Cookie::queue('chatPopupShow', 'false', 60);
        $this->chatPopupVisibility = false;
    }

    /**
     * Sends the chat message
     *
     * @return void
     */
    public function sendMessage()
    {
        $userId = auth()->user()->id;

        // Save the message
        Message::create([
            'room_id' => $this->roomId,
            'user_id' => $userId,
            'message' => $this->message,
        ]);

        // Remove the value of the message after saving
        $this->message = "";

        // Prompt the server that we sent a message
        $this->dispatchBrowserEvent('chat-send-message', [
            'room_id' => $this->roomId,
            'user_id' => $userId,
        ]);
    }

    public function render()
    {
        return view('livewire.chat-component');
    }
}
