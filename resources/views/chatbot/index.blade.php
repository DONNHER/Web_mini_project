@extends('layouts.app')

@section('title', 'AI Assistant - LendingSystem')

@section('header')
    <div class="flex items-center space-x-6">
        <a href="{{ route('home') }}" class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-[#1A1A1A] hover:bg-[#FF6B00] hover:text-white transition-all duration-300 shadow-sm group">
            <svg class="h-5 w-5 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
        </a>
        <div class="flex-1 flex justify-between items-end">
            <div>
                <span class="text-[#FF6B00] font-black uppercase tracking-[0.4em] text-[10px] mb-1 block">Neural Assistant</span>
                <h1 class="text-4xl font-black text-[#1A1A1A] uppercase tracking-tighter">AI <span class="text-[#FF6B00]">Assistant</span></h1>
            </div>
            <form action="{{ route('chatbot.clear') }}" method="POST">
                @csrf
                <button type="submit" class="text-[10px] font-black text-[#1A1A1A]/40 uppercase tracking-widest hover:text-red-500 transition no-underline border-b-2 border-transparent hover:border-red-500">
                    Purge History
                </button>
            </form>
        </div>
    </div>
@endsection

@section('content')
<div class="max-w-5xl mx-auto h-[75vh] flex flex-col card overflow-hidden">
    <!-- Chat Messages -->
    <div id="chat-window" class="flex-1 overflow-y-auto p-10 space-y-6 bg-white">
        @forelse($messages as $msg)
            <div class="flex {{ $msg['role'] === 'user' ? 'justify-end' : 'justify-start' }}">
                <div class="max-w-[70%] rounded-2xl px-6 py-4 text-sm font-bold leading-relaxed shadow-sm
                    {{ $msg['role'] === 'user'
                        ? 'bg-[#1A1A1A] text-white rounded-tr-none'
                        : 'bg-[#FEF6F0] text-[#1A1A1A] border border-[#1A1A1A]/5 rounded-tl-none' }}">
                    {!! nl2br(e($msg['content'])) !!}
                </div>
            </div>
        @empty
            <div class="text-center py-20">
                <div class="w-20 h-20 bg-[#FEF6F0] rounded-3xl flex items-center justify-center mx-auto mb-8 border border-[#FF6B00]/10">
                    <span class="text-3xl">👋</span>
                </div>
                <h3 class="text-2xl font-black text-[#1A1A1A] uppercase tracking-tighter">Hello Node</h3>
                <p class="text-[10px] font-black uppercase tracking-[0.3em] text-[#1A1A1A]/20 mt-4 max-w-xs mx-auto leading-relaxed">Identity confirmed. Ask me about registry protocols or capital inventory.</p>
            </div>
        @endforelse
    </div>

    <!-- Input Area -->
    <div class="p-8 bg-[#FEF6F0]/50 border-t border-[#1A1A1A]/5">
        <form id="chat-form" class="flex space-x-4">
            @csrf
            <div class="flex-1 relative">
                <input type="text"
                       id="user-input"
                       name="message"
                       placeholder="Enter directive..."
                       autocomplete="off"
                       class="w-full bg-white border-none rounded-2xl px-8 py-4 font-bold focus:ring-4 focus:ring-[#FF6B00]/5 placeholder-[#1A1A1A]/20 shadow-sm outline-none text-[#1A1A1A]">
            </div>
            <button type="submit"
                    id="send-btn"
                    class="btn-primary px-10 shadow-xl shadow-[#FF6B00]/20">
                <span>Execute</span>
                <svg id="loading-spinner" class="hidden animate-spin ml-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
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
        chatWindow.scrollTo({
            top: chatWindow.scrollHeight,
            behavior: 'smooth'
        });
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
            <div class="max-w-[70%] rounded-2xl px-6 py-4 text-sm font-bold leading-relaxed shadow-sm bg-[#1A1A1A] text-white rounded-tr-none animate-in fade-in slide-in-from-bottom-2 duration-300">
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
                <div class="max-w-[70%] rounded-2xl px-6 py-4 text-sm font-bold leading-relaxed shadow-sm bg-[#FEF6F0] text-[#1A1A1A] border border-[#1A1A1A]/5 rounded-tl-none animate-in fade-in slide-in-from-bottom-2 duration-500">
                    ${data.message.replace(/\n/g, '<br>')}
                </div>
            `;
            chatWindow.appendChild(aiDiv);
        } catch (error) {
            console.error('Error:', error);
            // Append error message
            const errDiv = document.createElement('div');
            errDiv.className = 'flex justify-center';
            errDiv.innerHTML = `
                <div class="px-4 py-2 bg-red-50 text-red-600 text-[10px] font-black uppercase tracking-widest rounded-lg border border-red-100">
                    Communication Failure: Check System Link
                </div>
            `;
            chatWindow.appendChild(errDiv);
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
