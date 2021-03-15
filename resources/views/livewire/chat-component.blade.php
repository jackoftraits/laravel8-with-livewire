<div>
    <button 
        class="open-button" 
        wire:click="showChatPopup"
    >Chat</button>
    @if ($this->chatPopupVisibility)
        <div class="chat-popup">
            <form class="form-container">
                <div>Room #: {{ $roomId }}</div>
                <label class="font-semibold">Message</label>
                <div
                    wire:ignore
                    id="messageBox"
                    class="border px-3 py-2 h-64 bg-gray-50 overflow-y-auto"
                    style="width: 280px"
                ></div>
                <textarea 
                    id="message"
                    class="focus:outline-none focus:bg-gray-100 w-full px-3 py-2"
                    placeholder="Type in your message..."
                    wire:model="message"
                    wire:keydown.enter.prevent="sendMessage"
                ></textarea>
                <button
                    type="button"
                    class="btn cancel" 
                    wire:click="closeChatPopup"              
                >Close</button>
            </form>
        </div>
    @endif

    @push('chat-websocket')
        <script>
            $(function () {
                /**
                 * Keeps the chat message box focus
                 * at the bottom.
                 *
                 * @param {string} elementId
                 */
                 function keepChatboxFocusAtBottom(elementId) {
                    var element = document.getElementById(elementId);
                    element.scrollTop = element.scrollHeight;
                }

                /**
                 * Returns the chat message proper format
                 *
                 * @param {string} id
                 * @param {string} username
                 * @param {string} message
                 */
                 function messageFormat(id, name, message) {
                    let userId = "{{ auth()->user()->id }}";
                    let color = id == userId ? "bg-blue-400" : "bg-green-400";
                    let alignment = id == userId ? "text-right" : "text-left";

                    return `
                        <div class="grid grid-cols-1 items-center gap-0">
                            <span class="${alignment} font-semibold text-sm">${name}</span>
                            <span class="${alignment} ${color} text-sm text-white px-3 py-2 rounded mb-2">${message}</span>
                        </div>
                    `;
                }

                // Instantiate a connection
                var chatConnection = clientSocket({ port: 3281 });

                // The messageBox element
                var messageBox = $("#messageBox");

                // The message element
                var message = $("#message");
                
                /**
                 * When the connection is open
                 */
                chatConnection.onopen = function () {
                    console.log("Chat connection is open!");
                    // Send the information of the client user here
                    chatConnection.send(JSON.stringify({
                        type: "info",
                        data: {
                            room_id: {{ $roomId }},
                            user_id: {{ auth()->user()->id }}
                        }
                    }));
                }

                /**
                 * Will receive messages from the websocket server
                 */
                chatConnection.onmessage = function (message) {
                    var result = JSON.parse(message.data);
                    var chatMessage = result.data;

                    if (result.type == "chatMessage") {
                        messageBox.append(messageFormat(
                            chatMessage.user_id,
                            chatMessage.name,
                            chatMessage.message
                        ));
                    }

                    keepChatboxFocusAtBottom("messageBox");
                }

                /**
                 * Send the prompt to the websocket server
                 */
                window.addEventListener('chat-send-message', event => {
                    console.log(event.detail);
                    chatConnection.send(JSON.stringify({
                        type: "chatMessage",
                        date: event.detail
                    }));
                });

                /**
                 * Reload the page
                 */
                window.addEventListener('reload-page', event => {
                   window.location.reload();
                });

            });
        </script>
    @endpush
</div>
