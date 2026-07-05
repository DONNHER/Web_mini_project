@extends('layouts.app')

@section('title', 'AI Assistant - LendingSystem')

@section('header')
    <div class="flex justify-between items-center">
        <h2 class="font-bold text-2xl text-white tracking-tight">
            🤖 AI Loan Assistant
        </h2>
        <form action="{{ route('chatbot.clear') }}" method="POST">
            @csrf
            <button type="submit" class="text-xs text-gray-500 hover:text-white transition uppercase tracking-widest font-bold">
                Clear History
            </button>
        </form>
    </div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto h-[70vh] flex flex-col bg-gray-800 rounded-2xl shadow-2xl border border-gray-700 overflow-hidden">
    <!-- Chat Messages -->
    <div id="chat-window" class="flex-1 overflow-y-auto p-6 space-y-4 scrollbar-thin scrollbar-thumb-gray-600">
        @forelse($messages as $msg)
            <div class="flex {{ $msg['role'] === 'user' ? 'justify-end' : 'justify-start' }}">
                <div class="max-w-[80%] rounded-2xl px-4 py-2 text-sm shadow-md
                    {{ $msg['role'] === 'user'
                        ? 'bg-blue-600 text-white rounded-tr-none'
                        : 'bg-gray-900 text-gray-200 border border-gray-700 rounded-tl-none' }}">
                    {!! nl2br(e($msg['content'])) !!}
                </div>
            </div>
        @empty
            <div class="text-center py-10">
                <div class="bg-gray-900/50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-700">
                    <span class="text-2xl">👋</span>
                </div>
                <h3 class="text-white font-bold">Hello! I'm your AI Assistant.</h3>
                <p class="text-gray-500 text-xs mt-1">Ask me anything about our loan products or repayment terms.</p>
            </div>
        @endforelse
    </div>

    <!-- Input Area -->
    <div class="p-4 bg-gray-900 border-t border-gray-700">
        <form id="chat-form" class="flex space-x-4">
            @csrf
            <input type="text"
                   id="user-input"
                   name="message"
                   placeholder="Type your message here..."
                   autocomplete="off"
                   class="flex-1 bg-gray-800 border-gray-700 rounded-xl text-white text-sm focus:ring-blue-500 focus:border-blue-500 placeholder-gray-500">
            <button type="submit"
                    id="send-btn"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-xl font-bold text-sm transition flex items-center shadow-lg shadow-blue-500/20">
                <span>Send</span>
                <svg id="loading-spinner" class="hidden animate-spin ml-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </button>
        </form>
    </div>
</div>

<script>
    const chatWindow = document.getElementById('chat-window');
    const chatForm = document.getElementById('chat-form');
    const userInput = document.getElementById('user-input');
    const sendBtn = document.getElementById('send-btn');
    const spinner = document.getElementById('loading-spinner');

    // Scroll to bottom
    const scrollToBottom = () => {
        chatWindow.scrollTop = chatWindow.scrollHeight;
    };
    scrollToBottom();

    chatForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const message = userInput.value.trim();
        if (!message) return;

        // Disable input
        userInput.value = '';
        userInput.disabled = true;
        sendBtn.disabled = true;
        spinner.classList.remove('hidden');

        // Append user message
        const userDiv = document.createElement('div');
        userDiv.className = 'flex justify-end';
        userDiv.innerHTML = `
            <div class="max-w-[80%] rounded-2xl px-4 py-2 text-sm shadow-md bg-blue-600 text-white rounded-tr-none">
                ${message}
            </div>
        `;
        chatWindow.appendChild(userDiv);
        scrollToBottom();

        try {
            const response = await fetch('{{ route("chatbot.send") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ message })
            });

            const data = await response.json();

            // Append AI response
            const aiDiv = document.createElement('div');
            aiDiv.className = 'flex justify-start';
            aiDiv.innerHTML = `
                <div class="max-w-[80%] rounded-2xl px-4 py-2 text-sm shadow-md bg-gray-900 text-gray-200 border border-gray-700 rounded-tl-none">
                    ${data.message.replace(/\n/g, '<br>')}
                </div>
            `;
            chatWindow.appendChild(aiDiv);
        } catch (error) {
            console.error('Error:', error);
        } finally {
            userInput.disabled = false;
            sendBtn.disabled = false;
            spinner.classList.add('hidden');
            userInput.focus();
            scrollToBottom();
        }
    });
</script>
@endsection
