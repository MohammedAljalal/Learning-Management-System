import { useState, useEffect, useRef } from 'react';
import axios from 'axios';

interface Message {
    id: number;
    role: 'user' | 'assistant';
    content: string;
    created_at: string;
}

interface AIChatbotProps {
    courseId: number;
    lessonId?: number | null;
}

export default function AIChatbot({ courseId, lessonId }: AIChatbotProps) {
    const [isOpen, setIsOpen] = useState(false);
    const [messages, setMessages] = useState<Message[]>([]);
    const [newMessage, setNewMessage] = useState('');
    const [isLoading, setIsLoading] = useState(false);
    const messagesEndRef = useRef<HTMLDivElement>(null);

    // Fetch initial messages when chat opens or lesson changes
    useEffect(() => {
        if (isOpen) {
            fetchMessages();
        }
    }, [isOpen, lessonId]);

    const scrollToBottom = () => {
        messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
    };

    useEffect(() => {
        scrollToBottom();
    }, [messages, isOpen]);

    const fetchMessages = async () => {
        try {
            const response = await axios.get('/api/ai-chat', {
                params: { course_id: courseId, lesson_id: lessonId }
            });
            setMessages(response.data.messages);
        } catch (error) {
            console.error('Failed to fetch chat messages', error);
        }
    };

    const sendMessage = async (e: React.FormEvent) => {
        e.preventDefault();
        if (!newMessage.trim() || isLoading) return;

        const messageText = newMessage.trim();
        setNewMessage('');
        
        // Optimistically add user message
        const tempId = Date.now();
        setMessages(prev => [...prev, {
            id: tempId,
            role: 'user',
            content: messageText,
            created_at: new Date().toISOString()
        }]);
        
        setIsLoading(true);

        try {
            const response = await axios.post('/api/ai-chat', {
                course_id: courseId,
                lesson_id: lessonId,
                message: messageText
            });

            // Replace temp message with actual ones from server
            setMessages(prev => [
                ...prev.filter(m => m.id !== tempId),
                response.data.userMessage,
                response.data.assistantMessage
            ]);
        } catch (error) {
            console.error('Failed to send message', error);
            // Revert optimistic update on error
            setMessages(prev => prev.filter(m => m.id !== tempId));
        } finally {
            setIsLoading(false);
        }
    };

    return (
        <div className="fixed bottom-6 end-6 z-50 flex flex-col items-end pointer-events-none" dir="rtl">
            
            {/* Chat Window */}
            {isOpen && (
                <div className="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl w-80 sm:w-96 mb-4 flex flex-col overflow-hidden pointer-events-auto transition-all transform origin-bottom-right" style={{ height: '500px', maxHeight: 'calc(100vh - 120px)' }}>
                    
                    {/* Header */}
                    <div className="bg-indigo-600 p-4 text-white flex items-center justify-between shadow-md z-10 shrink-0">
                        <div className="flex items-center gap-3">
                            <div className="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center">
                                <svg className="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <div>
                                <h3 className="font-bold text-sm">المساعد الذكي (AI)</h3>
                                <p className="text-xs text-indigo-200">اسألني أي شيء عن الدورة</p>
                            </div>
                        </div>
                        <button onClick={() => setIsOpen(false)} className="text-white hover:text-indigo-200 transition-colors bg-indigo-700 hover:bg-indigo-800 rounded-full p-1.5">
                            <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {/* Messages Area */}
                    <div className="flex-1 overflow-y-auto p-4 space-y-4 bg-slate-50 dark:bg-slate-950">
                        {messages.length === 0 ? (
                            <div className="text-center text-slate-500 dark:text-slate-400 mt-10 text-sm">
                                <div className="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/50 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <svg className="w-7 h-7 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                مرحباً! أنا المساعد الذكي لهذه الدورة.
                                <br />كيف يمكنني مساعدتك اليوم؟
                            </div>
                        ) : (
                            messages.map((msg) => (
                                <div key={msg.id} className={`flex ${msg.role === 'user' ? 'justify-end' : 'justify-start'}`}>
                                    <div className={`max-w-[85%] rounded-2xl px-4 py-2.5 text-sm shadow-sm ${
                                        msg.role === 'user' 
                                            ? 'bg-indigo-600 text-white rounded-tl-none' 
                                            : 'bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-tr-none'
                                    }`}>
                                        <p className="whitespace-pre-wrap leading-relaxed">{msg.content}</p>
                                    </div>
                                </div>
                            ))
                        )}
                        {isLoading && (
                            <div className="flex justify-start">
                                <div className="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl rounded-tr-none px-4 py-3 shadow-sm flex items-center gap-1.5">
                                    <span className="w-2 h-2 bg-indigo-400 rounded-full animate-bounce" style={{ animationDelay: '0ms' }}></span>
                                    <span className="w-2 h-2 bg-indigo-400 rounded-full animate-bounce" style={{ animationDelay: '150ms' }}></span>
                                    <span className="w-2 h-2 bg-indigo-400 rounded-full animate-bounce" style={{ animationDelay: '300ms' }}></span>
                                </div>
                            </div>
                        )}
                        <div ref={messagesEndRef} />
                    </div>

                    {/* Input Area */}
                    <div className="p-3 bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 shrink-0">
                        <form onSubmit={sendMessage} className="relative flex items-center">
                            <input
                                type="text"
                                value={newMessage}
                                onChange={e => setNewMessage(e.target.value)}
                                placeholder="اكتب سؤالك هنا..."
                                className="w-full bg-slate-100 dark:bg-slate-800 border-transparent focus:border-indigo-500 focus:bg-white dark:focus:bg-slate-900 focus:ring-0 rounded-xl py-2.5 px-4 pe-12 text-sm text-slate-900 dark:text-white transition-colors"
                                disabled={isLoading}
                            />
                            <button
                                type="submit"
                                disabled={!newMessage.trim() || isLoading}
                                className="absolute end-2 bg-indigo-600 hover:bg-indigo-700 text-white p-1.5 rounded-lg transition-colors disabled:opacity-50 disabled:hover:bg-indigo-600"
                            >
                                <svg className="w-4 h-4 transform -rotate-90 rtl:rotate-90" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            )}

            {/* Chat Toggle Button */}
            {!isOpen && (
                <button 
                    onClick={() => setIsOpen(true)} 
                    className="bg-indigo-600 hover:bg-indigo-700 text-white rounded-full p-4 shadow-lg shadow-indigo-600/30 transition-transform hover:scale-105 focus:outline-none flex items-center justify-center pointer-events-auto group"
                    title="المساعد الذكي للتعلم"
                >
                    <svg className="w-6 h-6 group-hover:animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                </button>
            )}
        </div>
    );
}
