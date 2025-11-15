<div class="max-w-5xl mx-auto py-10 px-4 sm:px-6 lg:px-8">

    <header class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">
            Pembahasan Soal: {{ $tryout->title }}
        </h1>
        <p class="text-gray-600">Sesi Selesai: {{ optional($userTryout->ended_at)->locale('id')->isoFormat('LLL') }} WIB</p>
    </header>

    {{-- Navigasi Panel (Opsional: Jika Anda ingin daftar soal di samping) --}}
    {{-- Namun, untuk tampilan yang menyerupai halaman pengerjaan, kita tampilkan satu per satu --}}

    {{-- Daftar Soal --}}
    <div class="space-y-12">
        @foreach ($userAnswers as $index => $userAnswer)
            @php
                $question = $userAnswer->question;
                $userSelectedAnswerId = $userAnswer->answer_id;
                // Asumsi: Relasi correctAnswer tersedia dan mengembalikan model Answer
                $correctAnswerId = optional($question->correctAnswer)->id; 
            @endphp

            <div id="question-{{ $question->id }}" class="bg-white shadow-xl rounded-lg p-6 border-l-4 
                @if($userSelectedAnswerId === $correctAnswerId) border-green-500 @elseif($userSelectedAnswerId !== null) border-red-500 @else border-gray-400 @endif
            ">
                
                {{-- HEADER SOAL --}}
                <div class="flex justify-between items-start mb-4 border-b pb-3">
                    <h3 class="text-xl font-bold text-gray-800">
                        Soal No. {{ $index + 1 }}
                        <span class="ml-3 text-sm font-medium text-blue-600">
                            ({{ optional($question->category)->name ?? 'Tanpa Kategori' }})
                        </span>
                    </h3>
                    <span class="px-3 py-1 text-sm rounded-full font-semibold 
                        @if($userSelectedAnswerId === $correctAnswerId) bg-green-100 text-green-700 @elseif($userSelectedAnswerId !== null) bg-red-100 text-red-700 @else bg-gray-100 text-gray-700 @endif">
                        {{ $userSelectedAnswerId === $correctAnswerId ? 'BENAR' : ($userSelectedAnswerId !== null ? 'SALAH' : 'TIDAK DIJAWAB') }}
                    </span>
                </div>

                {{-- TEKS SOAL --}}
                <div class="prose max-w-none mb-6 text-gray-700">
                    {!! $question->question_text !!}
                </div>

                {{-- PILIHAN JAWABAN --}}
                <div class="space-y-3">
                    @foreach ($question->answers->sortBy('order_column') as $answer)
                        @php
                            $isUserAnswer = ($userSelectedAnswerId == $answer->id);
                            $isCorrectAnswer = ($correctAnswerId == $answer->id);
                            
                            $baseClass = 'p-3 rounded-lg border text-gray-800 cursor-default flex items-start';
                            $highlightClass = '';

                            if ($isCorrectAnswer) {
                                $highlightClass = ' bg-green-100 border-green-500 font-bold';
                            } elseif ($isUserAnswer) {
                                $highlightClass = ' bg-red-100 border-red-500 line-through';
                            } else {
                                $highlightClass = ' bg-white border-gray-200 hover:bg-gray-50';
                            }
                        @endphp
                        
                        <div class="{{ $baseClass }} {{ $highlightClass }}">
                            {{-- Indikator (Check/Cross Mark) --}}
                            <span class="font-mono text-sm mr-4 mt-1 
                                @if($isCorrectAnswer) text-green-700 @elseif($isUserAnswer) text-red-700 @else text-gray-500 @endif">
                                @if($isCorrectAnswer) 
                                    &#9989; 
                                @elseif($isUserAnswer) 
                                    &#10060; 
                                @else 
                                    &#9679; 
                                @endif
                            </span>
                            {{-- Teks Jawaban --}}
                            <div class="prose max-w-none text-sm leading-relaxed flex-1">
                                {!! $answer->answer !!}
                            </div>
                        </div>
                    @endforeach
                </div>
                
                {{-- PEMBAHASAN --}}
                <div class="mt-8 pt-6 border-t border-dashed border-gray-300">
                    <details open>
                        <summary class="font-extrabold text-lg cursor-pointer text-indigo-700">
                            PENJELASAN (PEMBAHASAN)
                        </summary>
                        <div class="prose max-w-none mt-3 p-4 bg-indigo-50 rounded-lg text-gray-800">
                            @if ($question->explanation)
                                {!! $question->explanation !!}
                            @else
                                <p class="text-sm italic text-gray-600">Mohon maaf, pembahasan belum tersedia untuk soal ini.</p>
                            @endif
                        </div>
                    </details>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Tombol Kembali --}}
    <div class="mt-12 text-center">
        <a href="{{ route('tryout.my-results', $tryout->slug) }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150">
            &larr; Kembali ke Ringkasan Hasil
        </a>
    </div>

</div>