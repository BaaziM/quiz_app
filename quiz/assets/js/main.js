document.addEventListener('DOMContentLoaded', () => {
    const startBtn = document.getElementById('start-quiz-btn');
    const dashboardSection = document.getElementById('dashboard-section');
    const quizSection = document.getElementById('quiz-section');
    const resultSection = document.getElementById('result-section');
    
    let questions = [];
    let currentQuestionIndex = 0;
    let score = 0;
    
    if (startBtn) {
        startBtn.addEventListener('click', startQuiz);
    }

    async function startQuiz() {
        dashboardSection.classList.add('fade-out');
        
        setTimeout(async () => {
            dashboardSection.style.display = 'none';
            dashboardSection.classList.remove('fade-out');
            
            // Show loader
            quizSection.style.display = 'block';
            quizSection.innerHTML = '<div class="center-content"><div class="loader"></div></div>';
            quizSection.classList.add('fade-in');
            
            try {
                const response = await fetch('api/get_questions.php');
                const data = await response.json();
                
                if (data.status === 'success' && data.questions.length > 0) {
                    questions = data.questions;
                    currentQuestionIndex = 0;
                    score = 0;
                    renderQuizLayout();
                    loadQuestion();
                } else {
                    quizSection.innerHTML = `
                        <div class="glass-panel text-center">
                            <h2>No questions available!</h2>
                            <p class="mt-3">Please check back later.</p>
                            <a href="index.php" class="btn mt-3" style="width:auto">Go Back</a>
                        </div>`;
                }
            } catch (error) {
                console.error('Error fetching questions:', error);
            }
        }, 400);
    }

    function renderQuizLayout() {
        quizSection.innerHTML = `
            <div class="glass-panel">
                <div class="quiz-header">
                    <span id="question-counter">Question 1/${questions.length}</span>
                    <div class="progress-container">
                        <div class="progress-bar" id="progress-bar"></div>
                    </div>
                </div>
                <div class="question-container" id="question-container">
                    <!-- Question will be loaded here -->
                </div>
                <div class="quiz-footer">
                    <button class="btn btn-outline" id="next-btn" style="display:none; width:auto; margin-left:auto;">Next Question <i class="fas fa-arrow-right"></i></button>
                </div>
            </div>
        `;
        
        document.getElementById('next-btn').addEventListener('click', handleNext);
    }

    function loadQuestion() {
        const question = questions[currentQuestionIndex];
        const container = document.getElementById('question-container');
        
        // Update counter and progress
        document.getElementById('question-counter').innerText = `Question ${currentQuestionIndex + 1}/${questions.length}`;
        document.getElementById('progress-bar').style.width = `${((currentQuestionIndex) / questions.length) * 100}%`;
        
        container.classList.remove('fade-in');
        
        function escapeHTML(str) {
            if (!str) return '';
            return str.replace(/[&<>'"]/g, 
                tag => ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    "'": '&#39;',
                    '"': '&quot;'
                }[tag])
            );
        }

        // Render options
        const html = `
            <h2 class="question-text">${escapeHTML(question.question_text)}</h2>
            <div class="options-grid">
                <button class="option-btn" data-opt="a"><span class="option-letter">A</span> ${escapeHTML(question.option_a)}</button>
                <button class="option-btn" data-opt="b"><span class="option-letter">B</span> ${escapeHTML(question.option_b)}</button>
                <button class="option-btn" data-opt="c"><span class="option-letter">C</span> ${escapeHTML(question.option_c)}</button>
                <button class="option-btn" data-opt="d"><span class="option-letter">D</span> ${escapeHTML(question.option_d)}</button>
            </div>
        `;
        
        container.innerHTML = html;
        container.classList.add('fade-in');
        
        document.getElementById('next-btn').style.display = 'none';
        
        // Add event listeners to options
        const optionBtns = document.querySelectorAll('.option-btn');
        optionBtns.forEach(btn => {
            btn.addEventListener('click', selectOption);
        });
    }

    function selectOption(e) {
        const selectedBtn = e.currentTarget;
        const selectedOpt = selectedBtn.getAttribute('data-opt');
        const correctOpt = questions[currentQuestionIndex].correct_option;
        
        // Disable all options
        const optionBtns = document.querySelectorAll('.option-btn');
        optionBtns.forEach(btn => {
            btn.disabled = true;
            if (btn.getAttribute('data-opt') === correctOpt) {
                btn.classList.add('correct');
            }
        });
        
        if (selectedOpt === correctOpt) {
            score++;
        } else {
            selectedBtn.classList.add('wrong');
        }
        
        document.getElementById('next-btn').style.display = 'block';
    }

    function handleNext() {
        currentQuestionIndex++;
        if (currentQuestionIndex < questions.length) {
            const container = document.getElementById('question-container');
            container.classList.remove('fade-in');
            container.classList.add('fade-out');
            
            setTimeout(() => {
                container.classList.remove('fade-out');
                loadQuestion();
            }, 400);
        } else {
            finishQuiz();
        }
    }

    async function finishQuiz() {
        document.getElementById('progress-bar').style.width = `100%`;
        
        const container = document.querySelector('.quiz-container .glass-panel');
        container.classList.add('fade-out');
        
        setTimeout(async () => {
            container.classList.remove('fade-out');
            container.innerHTML = '<div class="center-content"><div class="loader"></div></div>';
            
            // Save score to backend
            try {
                const response = await fetch('api/save_score.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        score: score,
                        total: questions.length
                    })
                });
                const result = await response.json();
                
                showResults();
            } catch (error) {
                console.error("Failed to save score:", error);
                showResults(); // Still show results even if saving failed
            }
        }, 400);
    }

    function showResults() {
        const container = document.querySelector('.quiz-container .glass-panel');
        
        const percentage = Math.round((score / questions.length) * 100);
        let message = '';
        if (percentage >= 80) message = 'Excellent!';
        else if (percentage >= 50) message = 'Good Job!';
        else message = 'Keep Practicing!';
        
        container.innerHTML = `
            <div class="text-center fade-in" style="text-align: center;">
                <h2 style="font-size: 2.5rem; margin-bottom: 1rem; color: var(--primary)">${message}</h2>
                <div style="font-size: 4rem; font-weight: 700; margin-bottom: 2rem;">
                    ${score} <span style="font-size: 2rem; color: var(--text-muted)">/ ${questions.length}</span>
                </div>
                <p style="margin-bottom: 2rem; color: var(--text-muted)">You scored ${percentage}%</p>
                <div style="display: flex; gap: 1rem; justify-content: center;">
                    <a href="index.php" class="btn" style="width:auto">Back to Dashboard</a>
                </div>
            </div>
        `;
    }
});
