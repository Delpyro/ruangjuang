<?php

namespace App\Livewire\Admin\Question;

use App\Models\Question;
use App\Models\Answer;
use App\Models\Tryout;
use App\Models\QuestionCategory;
use App\Models\QuestionSubCategory;
use Livewire\Component;
use Livewire\WithPagination;
// [!code --] use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class QuestionManage extends Component
{
    // [!code --] use WithPagination, WithFileUploads;
    use WithPagination; // [!code ++]

    public $tryoutId;
    public $tryout;
    
    // Form properties
    public $currentQuestionNumber = 1;
    public $questionId;
    public $id_question_categories;
    public $id_question_sub_category;
    public $question = ''; 
    public $explanation = ''; 
    // [!code --] public $questionImage;
    public $is_active = true;
    public $isEdit = false;
    public $showModal = false;
    
    // Answers properties
    public $answers = [];
    // [!code --] public $answerImages = [];
    
    // Navigation properties
    public $questionsList = [];
    public $totalQuestions = 0;
    
    // Search and filter
    public $search = '';
    public $categoryFilter = '';
    public $subCategoryFilter = '';

    public $availableSubCategories = [];
    public $hasAnswerErrors = false;
    public $answerErrorMessages = [];
    public $fieldErrors = [];
    public $answerFieldErrors = [];

    protected $queryString = ['search', 'categoryFilter', 'subCategoryFilter'];

    protected function rules()
    {
        return [
            'id_question_categories' => 'required|exists:question_categories,id',
            'id_question_sub_category' => 'required|exists:question_sub_categories,id',
            'question' => 'required|string|min:5', 
            'explanation' => 'required|string|min:5',
            // [!code --] 'questionImage' => 'nullable|image|max:2048', 
            'is_active' => 'boolean',
            'answers.*.answer' => 'required|string|max:5000', 
            'answers.*.is_correct' => 'boolean',
            'answers.*.points' => 'integer|min:0|max:5',
            // [!code --] 'answerImages.*' => 'nullable|image|max:1024',
        ];
    }

    protected $messages = [
        'id_question_categories.required' => 'Kategori wajib dipilih.',
        'id_question_sub_category.required' => 'Sub Kategori wajib dipilih.',
        'question.required' => 'Pertanyaan wajib diisi.',
        'question.min' => 'Pertanyaan minimal 5 karakter.',
        'explanation.required' => 'Penjelasan wajib diisi.',
        'explanation.min' => 'Penjelasan minimal 5 karakter.',
        'answers.*.answer.required' => 'Jawaban :position wajib diisi.',
        'answers.*.answer.max' => 'Jawaban :position maksimal 500 karakter.',
        'answers.*.points.integer' => 'Poin :position harus berupa angka.',
        'answers.*.points.min' => 'Poin :position minimal 0.',
        'answers.*.points.max' => 'Poin :position maksimal 5.',
        // [!code --] 'questionImage.image' => 'File harus berupa gambar.',
        // [!code --] 'questionImage.max' => 'Gambar pertanyaan maksimal 2MB.',
        // [!code --] 'answerImages.*.image' => 'File gambar jawaban :position harus berupa gambar.',
        // [!code --] 'answerImages.*.max' => 'Gambar jawaban :position maksimal 1MB.',
    ];

    protected $validationAttributes = [
        'id_question_categories' => 'Kategori',
        'id_question_sub_category' => 'Sub Kategori',
        'explanation' => 'Penjelasan',
        'answers.0.answer' => 'jawaban A',
        'answers.1.answer' => 'jawaban B',
        'answers.2.answer' => 'jawaban C',
        'answers.3.answer' => 'jawaban D',
        'answers.4.answer' => 'jawaban E',
        'answers.5.answer' => 'jawaban F',
        'answers.6.answer' => 'jawaban G',
        'answers.7.answer' => 'jawaban H',
        // [!code --] 'answerImages.0' => 'gambar jawaban A',
        // [!code --] 'answerImages.1' => 'gambar jawaban B',
        // [!code --] 'answerImages.2' => 'gambar jawaban C',
        // [!code --] 'answerImages.3' => 'gambar jawaban D',
        // [!code --] 'answerImages.4' => 'gambar jawaban E',
        // [!code --] 'answerImages.5' => 'gambar jawaban F',
        // [!code --] 'answerImages.6' => 'gambar jawaban G',
        // [!code --] 'answerImages.7' => 'gambar jawaban H',
    ];

    public function mount($tryoutId)
    {
        $this->tryoutId = $tryoutId;
        $this->tryout = Tryout::with(['questions', 'activeQuestions'])->findOrFail($tryoutId);
        $this->initializeAnswers();
        $this->loadQuestionsNavigation();
        $this->loadAvailableSubCategories();
        $this->resetFieldErrors();
    }

    public function initializeAnswers()
    {
        if (empty($this->answers)) {
            $this->answers = [
                ['answer' => '', 'is_correct' => false, 'points' => 0],
                ['answer' => '', 'is_correct' => false, 'points' => 0],
                ['answer' => '', 'is_correct' => false, 'points' => 0],
                ['answer' => '', 'is_correct' => false, 'points' => 0],
                ['answer' => '', 'is_correct' => false, 'points' => 0],
            ];
        }
    }

    public function resetFieldErrors()
    {
        $this->fieldErrors = [];
        $this->answerFieldErrors = [];
    }

    public function updated($propertyName)
    {
        $this->resetErrorBag($propertyName); 
        $this->resetFieldErrors();
        
        try {
            // [!code --] if (in_array($propertyName, ['question', 'explanation', 'id_question_categories', 'id_question_sub_category', 'questionImage'])) {
            if (in_array($propertyName, ['question', 'explanation', 'id_question_categories', 'id_question_sub_category'])) { // [!code ++]
                $this->validateOnly($propertyName);
            }
            
            // [!code --] if (str_contains($propertyName, 'answers.') || str_contains($propertyName, 'answerImages.')) {
            if (str_contains($propertyName, 'answers.')) { // [!code ++]
                $this->validateOnly($propertyName);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->handleValidationErrors($e->errors());
        }
        
        if ($propertyName === 'id_question_categories') {
            $this->id_question_sub_category = null;
            $this->loadAvailableSubCategories();
        }
    }

    public function loadAvailableSubCategories()
    {
        if ($this->id_question_categories) {
            $this->availableSubCategories = QuestionSubCategory::where('question_category_id', $this->id_question_categories)
                ->active()
                ->get();
        } else {
            $this->availableSubCategories = collect();
        }
        
        if ($this->id_question_sub_category && !$this->availableSubCategories->contains('id', $this->id_question_sub_category)) {
            $this->id_question_sub_category = null;
        }
    }

    public function updatedIdQuestionCategories($value)
    {
        $this->id_question_sub_category = null;
        $this->loadAvailableSubCategories();
    }

    public function loadQuestionsNavigation()
    {
        $this->questionsList = Question::where('id_tryout', $this->tryoutId)
            ->orderBy('id')
            ->get(['id', 'question', 'is_active']); // [!code ++]        
        
            $this->totalQuestions = $this->questionsList->count();
        
        if (!$this->isEdit && $this->totalQuestions > 0) {
            $this->currentQuestionNumber = $this->totalQuestions + 1;
        }
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedCategoryFilter()
    {
        $this->subCategoryFilter = null;
        $this->resetPage();
    }

    public function updatedSubCategoryFilter()
    {
        $this->resetPage();
    }

    public function addAnswer()
    {
        if (count($this->answers) < 8) {
            $this->answers[] = ['answer' => '', 'is_correct' => false, 'points' => 0];
            $this->resetAnswerErrors();
            $this->resetFieldErrors();
        }
    }

    public function removeAnswer($index)
    {
        if (count($this->answers) > 1) {
            unset($this->answers[$index]);
            $this->answers = array_values($this->answers);
            
            // [!code --] if (isset($this->answerImages[$index])) {
            // [!code --]     unset($this->answerImages[$index]);
            // [!code --]     $this->answerImages = array_values($this->answerImages);
            // [!code --] }
            
            $this->resetAnswerErrors();
            $this->resetFieldErrors();
            
            $this->dispatch('answers-updated'); 
        }
    }

    public function navigateToQuestion($questionId)
    {
        $this->resetForm();
        $this->edit($questionId);
        
        $index = $this->questionsList->search(fn($item) => $item->id == $questionId);
        
        if ($index !== false) {
            $this->currentQuestionNumber = $index + 1;
        }
        
        $this->isEdit = true;
        $this->loadAvailableSubCategories();
        $this->resetFieldErrors();

        $this->dispatch('question-loaded');
    }

    public function navigateToNewQuestion()
    {
        $this->resetForm();
        $this->currentQuestionNumber = $this->totalQuestions + 1;
        $this->isEdit = false;
        $this->loadAvailableSubCategories();
        $this->resetFieldErrors();

        $this->dispatch('question-loaded');
        $this->dispatch('init-answers');
    }

    public function render()
    {
        $questions = Question::where('id_tryout', $this->tryoutId)
            ->when($this->search, fn($query) => $query->where('question', 'like', '%' . $this->search . '%'))
            ->when($this->categoryFilter, fn($query) => $query->where('id_question_categories', $this->categoryFilter))
            ->when($this->subCategoryFilter, fn($query) => $query->where('id_question_sub_category', $this->subCategoryFilter))
            ->with(['category', 'subCategory', 'answers'])
            ->orderBy('id')
            ->paginate(10);

        $categories = QuestionCategory::active()->get();

        return view('livewire.admin.question.question-manage', [
            'questions' => $questions,
            'categories' => $categories,
            'subCategories' => $this->availableSubCategories,
            'filterCategories' => QuestionCategory::active()->get(),
            'filterSubCategories' => $this->categoryFilter 
                ? QuestionSubCategory::where('question_category_id', $this->categoryFilter)->active()->get()
                : collect(),
        ])->layout('layouts.admin');
    }

    public function openModal($edit = false, $id = null)
    {
        $this->resetForm();
        $this->isEdit = $edit;

        if ($edit && $id) {
            $this->edit($id);
            $index = $this->questionsList->search(fn($item) => $item->id == $id);
            $this->currentQuestionNumber = $index !== false ? $index + 1 : $this->totalQuestions + 1;
        } else {
            $this->currentQuestionNumber = $this->totalQuestions + 1;
        }

        $this->loadAvailableSubCategories();
        $this->showModal = true;
        $this->resetFieldErrors();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetErrorBag();
        // [!code --] $this->reset(['questionImage', 'answerImages', 'hasAnswerErrors', 'answerErrorMessages']);
        $this->reset(['hasAnswerErrors', 'answerErrorMessages']); // [!code ++]
        $this->loadQuestionsNavigation();
        $this->resetFieldErrors();
    }

    public function resetForm()
    {
        $this->reset([
            'questionId', 'id_question_categories', 'id_question_sub_category',
            'question', 'explanation', 'is_active'
        ]);
        $this->resetErrorBag();
        $this->initializeAnswers();
        $this->is_active = true;
        // [!code --] $this->questionImage = null;
        // [!code --] $this->answerImages = [];
        $this->isEdit = false;
        $this->resetAnswerErrors();
        $this->resetFieldErrors();
    }

    public function resetAnswerErrors()
    {
        $this->hasAnswerErrors = false;
        $this->answerErrorMessages = [];
    }

    // ==========================================================
    // PERBAIKAN UTAMA: Method 'save' diubah untuk refresh halaman
    // ==========================================================
    public function save()
    {
        if ($this->isEdit) {
            // --- LOGIKA UPDATE ---
            $success = $this->update(); // Panggil update()

            if ($success) {
                // Berhasil update, siapkan form untuk soal baru
                session()->flash('success', 'Question berhasil diperbarui.');
                return redirect(request()->header('Referer')); // Lakukan full page refresh
            }
            
        } else {
            // --- LOGIKA CREATE ---
            $success = $this->create(); // Panggil create()

            if ($success) {
                // Berhasil create, siapkan form untuk soal baru
                session()->flash('success', 'Question berhasil ditambahkan.');
                return redirect(request()->header('Referer')); // Lakukan full page refresh
            }
        }
        
        return null;
    }


    // ==========================================================
    // METHOD 'create' DIPERBARUI
    // Hanya me-return true/false, navigasi ditangani 'save'
    // ==========================================================
    public function create()
    {
        try {
            $this->resetAnswerErrors();
            $this->resetFieldErrors();
            $this->resetErrorBag(); 

            $this->validate();
            $this->validateAnswers();

            // [!code --] $questionImagePath = null;
            // [!code --] if ($this->questionImage) {
            // [!code --]     $questionImagePath = $this->questionImage->store('questions', 'public');
            // [!code --] }

            $question = Question::create([
                'id_tryout' => $this->tryoutId,
                'id_question_categories' => $this->id_question_categories,
                'id_question_sub_category' => $this->id_question_sub_category,
                'question' => $this->question,
                'explanation' => $this->explanation,
                // [!code --] 'image' => $questionImagePath,
                'is_active' => $this->is_active,
            ]);

            foreach ($this->answers as $index => $answerData) {
                // [!code --] $answerImagePath = null;
                // [!code --] if (isset($this->answerImages[$index]) && $this->answerImages[$index]) {
                // [!code --]     $answerImagePath = $this->answerImages[$index]->store('answers', 'public');
                // [!code --] }

                Answer::create([
                    'id_question' => $question->id,
                    'answer' => $answerData['answer'],
                    // [!code --] 'image' => $answerImagePath,
                    'is_correct' => $answerData['is_correct'],
                    'points' => $answerData['points'] ?: 0,
                ]);
            }

            $this->loadQuestionsNavigation();
            
            return true;
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->handleValidationErrors($e->errors());
            session()->flash('error', 'Terdapat kesalahan dalam pengisian form. Silakan periksa kembali.');
            return false;
        } catch (\Exception $e) {
            $this->hasAnswerErrors = true;
            $this->answerErrorMessages[] = $e->getMessage();
            session()->flash('error', 'Gagal menambahkan question: ' . $e->getMessage());
            return false;
        }
    }

    public function edit($id)
    {
        $question = Question::with('answers')->findOrFail($id);
        $this->questionId = $id;
        $this->id_question_categories = $question->id_question_categories;
        $this->id_question_sub_category = $question->id_question_sub_category;
        $this->question = $question->question; 
        $this->explanation = $question->explanation; 
        $this->is_active = $question->is_active;

        $this->answers = [];
        foreach ($question->answers as $answer) {
            $this->answers[] = [
                'answer' => $answer->answer,
                'is_correct' => $answer->is_correct,
                'points' => $answer->points,
            ];
        }

        while (count($this->answers) < 5) {
            $this->answers[] = ['answer' => '', 'is_correct' => false, 'points' => 0];
        }

        $this->isEdit = true;
        $this->resetAnswerErrors();
        $this->resetFieldErrors();
        
        $this->dispatch('init-answers'); 
    }

    // ==========================================================
    // METHOD 'update' DIPERBARUI
    // Hanya me-return true/false, navigasi ditangani 'save'
    // ==========================================================
    public function update()
    {
        try {
            $this->resetAnswerErrors();
            $this->resetFieldErrors();
            $this->resetErrorBag(); 

            $this->validate();
            $this->validateAnswers();

            $question = Question::findOrFail($this->questionId);

            // [!code --] $questionImagePath = $question->image;
            // [!code --] if ($this->questionImage) {
            // [!code --]     if ($questionImagePath) {
            // [!code --]         Storage::disk('public')->delete($questionImagePath);
            // [!code --]     }
            // [!code --]     $questionImagePath = $this->questionImage->store('questions', 'public');
            // [!code --] }

            $question->update([
                'id_question_categories' => $this->id_question_categories,
                'id_question_sub_category' => $this->id_question_sub_category,
                'question' => $this->question,
                'explanation' => $this->explanation,
                // [!code --] 'image' => $questionImagePath,
                'is_active' => $this->is_active,
            ]);

            foreach ($question->answers as $oldAnswer) {
                // [!code --] if ($oldAnswer->image) {
                // [!code --]     Storage::disk('public')->delete($oldAnswer->image);
                // [!code --] }
                $oldAnswer->delete();
            }
            
            foreach ($this->answers as $index => $answerData) {
                // [!code --] $answerImagePath = null;
                
                // [!code --] if (isset($this->answerImages[$index]) && $this->answerImages[$index]) {
                // [!code --]     $answerImagePath = $this->answerImages[$index]->store('answers', 'public');
                // [!code --] }

                Answer::create([
                    'id_question' => $question->id,
                    'answer' => $answerData['answer'],
                    // [!code --] 'image' => $answerImagePath,
                    'is_correct' => $answerData['is_correct'],
                    'points' => $answerData['points'] ?: 0,
                ]);
            }

            $this->loadQuestionsNavigation();
            
            return true; // Kembalikan status sukses
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->handleValidationErrors($e->errors());
            session()->flash('error', 'Terdapat kesalahan dalam pengisian form. Silakan periksa kembali.');
            return false; // Kembalikan status gagal
        } catch (\Exception $e) {
            $this->hasAnswerErrors = true;
            $this->answerErrorMessages[] = $e->getMessage();
            session()->flash('error', 'Gagal memperbarui question: ' . $e->getMessage());
            return false; // Kembalikan status gagal
        }
    }

    protected function validateAnswers()
    {
        $correctAnswers = array_filter($this->answers, fn($answer) => $answer['is_correct']);

        if (count($correctAnswers) === 0) {
            throw new \Exception('Setidaknya satu jawaban harus ditandai sebagai benar.');
        }

        $answerTexts = array_map(fn($answer) => trim(strtolower(strip_tags($answer['answer']))), 
            array_filter($this->answers, fn($answer) => !empty(trim(strip_tags($answer['answer']))))); 

        $uniqueAnswers = array_unique($answerTexts);
        
        if (count($answerTexts) !== count($uniqueAnswers)) {
            throw new \Exception('Terdapat jawaban yang duplikat. Setiap jawaban harus unik.');
        }

        foreach ($this->answers as $index => $answer) {
            if ($answer['is_correct'] && empty(trim(strip_tags($answer['answer'])))) {
                $letter = $this->getAnswerLetter($index);
                throw new \Exception("Jawaban {$letter} yang ditandai sebagai benar harus memiliki teks jawaban.");
            }
        }
    }

    protected function handleValidationErrors($errors)
    {
        $this->resetFieldErrors(); 
        $this->hasAnswerErrors = false; 
        $this->answerErrorMessages = []; 

        foreach ($errors as $field => $messages) {
            // [!code --] if (str_contains($field, 'answers.') || str_contains($field, 'answerImages.')) {
            if (str_contains($field, 'answers.')) { // [!code ++]
                $this->answerFieldErrors[$field] = $messages[0];
                $this->hasAnswerErrors = true; 
                
                if (!in_array($messages[0], $this->answerErrorMessages)) {
                    $this->answerErrorMessages[] = $messages[0];
                }
            } else {
                $this->fieldErrors[$field] = $messages[0];
            }
        }
    }

    public function confirmDelete($questionId)
    {
        $this->dispatch('show-delete-confirmation', questionId: $questionId);
    }

    public function deleteQuestion($id)
    {
        try {
            $question = Question::findOrFail($id);
            
            // [!code --] if ($question->image) {
            // [!code --]     Storage::disk('public')->delete($question->image);
            // [!code --] }
            
            // [!code --] foreach ($question->answers as $answer) {
            // [!code --]     if ($answer->image) {
            // [!code --]         Storage::disk('public')->delete($answer->image);
            // [!code --]     }
            // [!code --] }
            
            $question->delete(); 
            
            $this->loadQuestionsNavigation();
            
            if ($this->questionId == $id) {
                $this->resetForm();
                $this->currentQuestionNumber = $this->totalQuestions > 0 ? $this->totalQuestions + 1 : 1;
            }
            
            session()->flash('success', 'Question berhasil dihapus.');
            
            if ($this->showModal) {
                $this->closeModal();
            }
            
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus question: ' . $e->getMessage());
        }
    }

    public function toggleStatus($id)
    {
        try {
            $question = Question::findOrFail($id);
            $question->update(['is_active' => !$question->is_active]);
            
            $this->loadQuestionsNavigation();
            session()->flash('success', 'Status question berhasil diubah.');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengubah status: ' . $e->getMessage());
        }
    }

    public function updateAnswerCorrect($index)
    {
        // (Logika updateAnswerCorrect jika diperlukan)
    }

    public function getAnswerLetter($index)
    {
        $letters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
        return $letters[$index] ?? chr(65 + $index);
    }

    // [!code --] public function removeQuestionImage()
    // [!code --] {
    // [!code --]     $this->questionImage = null;
    // [!code --]     $this->resetFieldErrors();
    // [!code --] }
    // [!code --] 
    // [!code --] public function removeAnswerImage($index)
    // [!code --] {
    // [!code --]     if (isset($this->answerImages[$index])) {
    // [!code --]         unset($this->answerImages[$index]);
    // [!code --]         $this->answerImages = array_values($this->answerImages);
    // [!code --]         $this->resetFieldErrors();
    // [!code --]     }
    // [!code --] }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter()
    {
        $this->resetPage();
    }

    public function updatingSubCategoryFilter()
    {
        $this->resetPage();
    }

    public function getFieldError($fieldName)
    {
        return $this->fieldErrors[$fieldName] ?? $this->getErrorBag()->first($fieldName);
    }

    public function getAnswerFieldError($answerIndex, $field)
    {
        $fieldName = "answers.{$answerIndex}.{$field}";
        return $this->answerFieldErrors[$fieldName] ?? $this->getErrorBag()->first($fieldName);
    }

    // [!code --] public function getAnswerImageError($answerIndex)
    // [!code --] {
    // [!code --]     $fieldName = "answerImages.{$answerIndex}";
    // [!code --]     return $this->answerFieldErrors[$fieldName] ?? $this->getErrorBag()->first($fieldName);
    // [!code --] }
}