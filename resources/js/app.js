import Alpine from 'alpinejs';
import { api, getUser, clearSession, categoryColor } from './api.js';

window.Alpine = Alpine;

// ---------- Global Success Modal Helper ----------
window.notifySuccess = function(msg = 'Aksi berhasil dilakukan!') {
    window.dispatchEvent(new CustomEvent('show-center-success', { detail: { message: msg } }));
};

Alpine.data('centerSuccessModal', () => ({
    show: false,
    message: '',
    timeout: null,

    init() {
        window.addEventListener('show-center-success', (e) => {
            this.message = e.detail?.message || 'Aksi berhasil dilakukan!';
            this.show = true;
            clearTimeout(this.timeout);
            this.timeout = setTimeout(() => {
                this.show = false;
            }, 1800);
        });
    }
}));

// ---------- Global Auth Store ----------
Alpine.store('auth', {
    user: getUser(),
    async logout() {
        try {
            await api.logout();
        } catch {
            clearSession();
        } finally {
            this.user = null;
            window.location.href = '/login';
        }
    },
});

// ---------- 1. Login Page ----------
Alpine.data('loginPage', () => ({
    email: '',
    password: '',
    error: '',
    loading: false,

    async submit() {
        this.loading = true;
        this.error = '';
        try {
            const res = await api.login({ email: this.email, password: this.password });
            const user = getUser() || res.user;
            Alpine.store('auth').user = user;
            window.notifySuccess('Berhasil login!');
            setTimeout(() => {
                window.location.href = ['admin', 'teacher'].includes(user?.role) ? '/admin/dashboard' : '/beranda';
            }, 800);
        } catch (e) {
            this.error = e.message;
        } finally {
            this.loading = false;
        }
    },
}));

// ---------- 2. Register Page ----------
Alpine.data('registerPage', () => ({
    name: '',
    email: '',
    password: '',
    error: '',
    loading: false,

    async submit() {
        this.loading = true;
        this.error = '';
        try {
            const res = await api.register({ name: this.name, email: this.email, password: this.password });
            Alpine.store('auth').user = getUser() || res.user;
            window.notifySuccess('Registrasi berhasil!');
            setTimeout(() => {
                window.location.href = '/beranda';
            }, 800);
        } catch (e) {
            this.error = e.message;
        } finally {
            this.loading = false;
        }
    },
}));

// ---------- 3. Beranda (List Ujian Santri) ----------
Alpine.data('berandaPage', () => ({
    loading: true,
    error: '',
    exams: {},
    openCategory: null,
    categoryOrder: ['Bahasa', 'IT', 'Karakter'],

    get orderedCategories() {
        const keys = Object.keys(this.exams);
        const ordered = this.categoryOrder.filter((c) => keys.includes(c));
        const rest = keys.filter((k) => !this.categoryOrder.includes(k));
        return [...ordered, ...rest];
    },

    toggleCategory(category) {
        this.openCategory = this.openCategory === category ? null : category;
    },

    isExpanded(category) {
        return this.openCategory === category;
    },

    categoryDotClass(category) {
        const key = category.toLowerCase();
        if (key === 'bahasa') return 'bg-brand-blue';
        if (key === 'it') return 'bg-brand-green';
        if (key === 'karakter') return 'bg-brand-orange';
        return 'bg-brand-blue';
    },

    categoryBorderClass(category) {
        const key = category.toLowerCase();
        if (key === 'bahasa') return 'border-l-brand-blue';
        if (key === 'it') return 'border-l-brand-green';
        if (key === 'karakter') return 'border-l-brand-orange';
        return 'border-l-brand-blue';
    },

    examButtonLabel(item) {
        if (item.retake_allowed) return 'Kerjakan Ulang';
        if (item.completed) return 'Lihat Hasil';
        return 'Mulai Ujian';
    },

    examButtonClass(item) {
        if (item.retake_allowed) return '';
        if (item.completed) return 'opacity-90 bg-slate-600 hover:bg-slate-700';
        return '';
    },

    async init() {
        try {
            const json = await api.getExams();
            this.exams = json?.data ?? json ?? {};
            this.openCategory = this.orderedCategories[0] ?? null;
        } catch (e) {
            this.error = e.message;
        } finally {
            this.loading = false;
        }
    },
}));

// ---------- 4. Pengerjaan Ujian (Flow UI & Timer Per Soal) ----------
Alpine.data('examFlow', (examId) => ({
    examId,
    step: 'loading',
    exam: null,
    periodTitle: '',
    lockMessage: '',
    questions: [],
    currentIndex: 0,
    jawaban: {},
    imageFiles: {},
    imagePreviews: {},
    
    questionTimeRemaining: 60,
    currentQuestionTimeLimit: 60,
    timerInterval: null,

    get currentQuestion() {
        if (!this.questions || this.questions.length === 0) return null;
        return this.questions[this.currentIndex] || null;
    },
    get isLast() {
        return this.currentIndex >= (this.questions.length - 1);
    },
    get answeredCount() {
        const textKeys = Object.keys(this.jawaban).filter(k => this.jawaban[k] !== undefined && this.jawaban[k] !== '');
        const imgKeys = Object.keys(this.imageFiles).filter(k => this.imageFiles[k] !== undefined && this.imageFiles[k] !== null);
        return new Set([...textKeys, ...imgKeys]).size;
    },
    get progressPercentage() {
        if (!this.questions.length) return 0;
        return Math.round((this.answeredCount / this.questions.length) * 100);
    },

    async init() {
        const token = localStorage.getItem('ts_token') || localStorage.getItem('token');
        try {
            const res = await fetch(`/api/exams/${this.examId}`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });
            const json = await res.json();

            if (res.status === 403) {
                this.step = 'closed';
                this.periodTitle = json?.period_title || 'Ujian Terkunci';
                this.lockMessage = json?.message || 'Ujian belum dapat diakses.';
                return;
            }

            if (!res.ok) {
                this.step = 'closed';
                this.lockMessage = json?.message || 'Terjadi kendala saat memuat ujian.';
                return;
            }

            this.exam = json?.data?.exam || null;
            this.questions = json?.data?.questions || [];

            if (json?.data?.completed && !json?.data?.retake_allowed) {
                window.location.href = '/dashboard';
                return;
            }

            if (this.questions.length === 0) {
                this.step = 'empty';
            } else {
                this.step = 'ready';
            }
        } catch (err) {
            console.error(err);
            this.step = 'closed';
            this.lockMessage = 'Gagal terhubung ke server.';
        }
    },

    resetQuestionTimer() {
        clearInterval(this.timerInterval);
        this.currentQuestionTimeLimit = Number(this.currentQuestion?.time_limit_seconds) || 60;
        this.questionTimeRemaining = this.currentQuestionTimeLimit;

        this.timerInterval = setInterval(() => {
            if (this.questionTimeRemaining > 0) {
                this.questionTimeRemaining--;
            } else {
                clearInterval(this.timerInterval);
                if (this.isLast) {
                    this.finishExam();
                } else {
                    this.nextQuestion();
                }
            }
        }, 1000);
    },

    handleImageUpload(questionId, event) {
        const file = event.target.files[0];
        if (!file) return;

        this.imageFiles[questionId] = file;
        this.jawaban[questionId] = `[Uploaded: ${file.name}]`;

        const reader = new FileReader();
        reader.onload = (e) => {
            this.imagePreviews[questionId] = e.target.result;
        };
        reader.readAsDataURL(file);
    },

    removeUploadedImage(questionId) {
        delete this.imageFiles[questionId];
        delete this.imagePreviews[questionId];
        delete this.jawaban[questionId];
    },

    startExam() {
        if (!this.questions.length) return;
        this.step = 'exam';
        this.currentIndex = 0;
        this.resetQuestionTimer();
    },

    nextQuestion() {
        if (!this.isLast) {
            this.currentIndex++;
            this.resetQuestionTimer();
        }
    },

    prevQuestion() {
        if (this.currentIndex > 0) {
            this.currentIndex--;
            this.resetQuestionTimer();
        }
    },

    async finishExam() {
        clearInterval(this.timerInterval);
        this.step = 'analyzing';

        const token = localStorage.getItem('ts_token') || localStorage.getItem('token');
        const formData = new FormData();
        formData.append('exam_id', this.examId);

        let idx = 0;
        for (const q of this.questions) {
            formData.append(`answers[${idx}][question_id]`, q.id);
            formData.append(`answers[${idx}][answer_text]`, this.jawaban[q.id] || '');
            if (this.imageFiles[q.id]) {
                formData.append(`answers[${idx}][file]`, this.imageFiles[q.id]);
            }
            idx++;
        }

        try {
            await fetch('/api/exams/submit', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                },
                body: formData
            });

            if (window.notifySuccess) {
                window.notifySuccess('Ujian selesai & jawaban terkirim!');
            }

            setTimeout(() => {
                window.location.href = '/dashboard';
            }, 2000);
        } catch (err) {
            console.error(err);
            setTimeout(() => {
                window.location.href = '/dashboard';
            }, 1500);
        }
    }
}));

// ---------- 5. Portofolio ----------
Alpine.data('portofolioPage', () => ({
    links: [''],
    files: [],
    maxFiles: 5,
    submitting: false,
    error: '',
    success: false,

    addLink() {
        this.links.push('');
    },

    removeLink(index) {
        if (this.links.length > 1) {
            this.links.splice(index, 1);
        }
    },

    addFiles(fileList) {
        const room = this.maxFiles - this.files.length;
        this.files.push(...Array.from(fileList).slice(0, room));
    },

    removeFile(i) {
        this.files.splice(i, 1);
    },

    async submit() {
        this.submitting = true;
        this.error = '';
        this.success = false;

        try {
            const validLinks = this.links.filter(l => l && l.trim() !== '').join(', ');
            const formData = new FormData();
            if (validLinks) formData.append('links', validLinks);
            this.files.forEach((f) => formData.append('files[]', f));

            await api.submitPortfolio(formData);
            this.success = true;
            this.files = [];
            this.links = [''];
            window.notifySuccess('Portofolio berhasil diunggah!');
            setTimeout(() => {
                window.location.href = '/dashboard';
            }, 1200);
        } catch (e) {
            this.error = e.message;
        } finally {
            this.submitting = false;
        }
    },
}));

// ---------- 6. Profile & Prediksi Hasil ----------
Alpine.data('profilePage', () => ({
    loading: true,
    error: '',
    profile: null,
    stats: [],
    portfolio: null,
    languageLevel: '-',
    languageAccuracy: 0,
    itLevel: '-',
    itAccuracy: 0,

    get totalAnswered() {
        return this.stats.reduce((sum, item) => sum + (Number(item.answered_count) || 0), 0);
    },

    get overallProgress() {
        if (!this.stats.length) return 0;
        const totalExams = this.stats.length;
        const completedExams = this.stats.filter(s => Number(s.answered_count) > 0).length;
        return Math.min(100, Math.round((completedExams / totalExams) * 100));
    },

    async init() {
        try {
            const json = await api.getDashboard();
            const data = json?.data ?? json;
            this.profile = {
                ...data.student,
                role: Alpine.store('auth').user?.role ?? 'student'
            };
            this.stats = data.exam_stats ?? [];
            this.portfolio = data.portfolio ?? null;
            this.calculatePredictions();
        } catch (e) {
            this.error = e.message;
        } finally {
            this.loading = false;
        }
    },

    calculatePredictions() {
        const bahasaStats = this.stats.filter((s) => s.category?.toLowerCase() === 'bahasa');
        if (bahasaStats.length > 0) {
            const avg = bahasaStats.reduce((acc, curr) => acc + (Number(curr.mc_accuracy_pct) || 0), 0) / bahasaStats.length;
            this.languageAccuracy = Math.round(avg);

            if (avg < 20) this.languageLevel = 'A1';
            else if (avg < 40) this.languageLevel = 'A2';
            else if (avg < 60) this.languageLevel = 'B1';
            else if (avg < 75) this.languageLevel = 'B2';
            else if (avg < 90) this.languageLevel = 'C1';
            else this.languageLevel = 'C2';
        }

        const itStats = this.stats.filter((s) => s.category?.toLowerCase() === 'it');
        if (itStats.length > 0) {
            const avg = itStats.reduce((acc, curr) => acc + (Number(curr.mc_accuracy_pct) || 0), 0) / itStats.length;
            this.itAccuracy = Math.round(avg);

            if (avg < 34) this.itLevel = 'Rookie';
            else if (avg < 67) this.itLevel = 'Amateur';
            else this.itLevel = 'Pro';
        }
    },
}));

// ---------- Animated Counter Helper ----------
Alpine.data('animatedCounter', (target = 0, ms = 700) => ({
    display: 0,
    start() {
        const end = Number(target) || 0;
        const tick = performance.now();
        const step = (now) => {
            const progress = Math.min(1, (now - tick) / ms);
            this.display = Math.round(end * progress);
            if (progress < 1) requestAnimationFrame(step);
        };
        requestAnimationFrame(step);
    },
}));

// ---------- 7. Admin Dashboard (Daftar Santri & Filter Periode) ----------
Alpine.data('adminDashboardPage', () => ({
    loading: true,
    error: '',
    students: [],
    availablePeriods: [],
    searchQuery: '',
    filterPeriod: '',
    sortBy: 'default',
    sortDropdownOpen: false,
    periodDropdownOpen: false,
    currentPage: 1,
    itemsPerPage: 10,
    activeStudent: null,
    modalAnim: false,
    retakeBusy: null,

    async init() {
        const user = getUser();
        if (!user || !['admin', 'teacher'].includes(user.role)) {
            this.error = 'Akses ditolak. Halaman ini khusus Admin / Guru.';
            this.loading = false;
            return;
        }

        this.$watch('searchQuery', () => { this.currentPage = 1; });
        this.$watch('filterPeriod', () => { this.currentPage = 1; });

        try {
            const res = await api.getAdminStudents();
            this.students = res?.data ?? [];
            
            if (res?.periods && Array.isArray(res.periods) && res.periods.length > 0) {
                this.availablePeriods = res.periods;
            } else {
                const setP = new Set();
                this.students.forEach(s => {
                    (s.periods || []).forEach(p => setP.add(p));
                    (s.exam_stats || []).forEach(e => { if (e.period_title) setP.add(e.period_title); });
                });
                this.availablePeriods = Array.from(setP);
            }
        } catch (e) {
            this.error = e.message;
        } finally {
            this.loading = false;
        }
    },

    studentName(student) {
        return student?.name || student?.student_name || '-';
    },

    studentEmail(student) {
        return student?.email || student?.student_email || '';
    },

    testsDone(student) {
        return (student?.exam_stats || []).filter(e => e.completed).length;
    },

    portfolioFiles(student) {
        if (Array.isArray(student?.portfolio?.files)) return student.portfolio.files;
        if (Array.isArray(student?.files)) return student.files;
        return [];
    },

    studentPeriods(s) {
        if (Array.isArray(s.periods) && s.periods.length > 0) return s.periods;
        const periods = new Set();
        (s.exam_stats || []).forEach(a => {
            if (a.period_title) periods.add(a.period_title);
        });
        return Array.from(periods);
    },

    get filteredStudents() {
        return this.students.filter(s => {
            const q = this.searchQuery.toLowerCase().trim();
            const nameMatch = this.studentName(s).toLowerCase().includes(q);
            const emailMatch = this.studentEmail(s).toLowerCase().includes(q);
            const matchesQuery = !q || (nameMatch || emailMatch);

            const studentP = this.studentPeriods(s);
            const matchesPeriod = !this.filterPeriod || studentP.includes(this.filterPeriod) || (s.exam_stats || []).some(e => e.period_title === this.filterPeriod);

            return matchesQuery && matchesPeriod;
        });
    },

    get sortedStudents() {
        const list = [...this.filteredStudents];
        if (this.sortBy === 'highest_score') {
            return list.sort((a, b) => {
                const maxA = Math.max(0, ...Object.values(a.career_predictions || {}));
                const maxB = Math.max(0, ...Object.values(b.career_predictions || {}));
                return maxB - maxA;
            });
        }
        if (this.sortBy === 'alphabetical') {
            return list.sort((a, b) => this.studentName(a).localeCompare(this.studentName(b)));
        }
        return list;
    },

    get paginatedStudents() {
        const start = (this.currentPage - 1) * this.itemsPerPage;
        return this.sortedStudents.slice(start, start + this.itemsPerPage);
    },

    get totalPages() {
        return Math.max(1, Math.ceil(this.sortedStudents.length / this.itemsPerPage));
    },

    setSort(type) {
        this.sortBy = type;
        this.sortDropdownOpen = false;
        this.currentPage = 1;
    },

    setPeriod(period) {
        this.filterPeriod = period;
        this.periodDropdownOpen = false;
        this.currentPage = 1;
    },

    nextPage() {
        if (this.currentPage < this.totalPages) this.currentPage++;
    },

    prevPage() {
        if (this.currentPage > 1) this.currentPage--;
    },

    get summaryTotal() {
        return this.students.length;
    },

    get summaryTestsDone() {
        return this.students.reduce((sum, s) => sum + this.testsDone(s), 0);
    },

    get summaryAvgHighest() {
        if (!this.students.length) return 0;
        const sum = this.students.reduce((acc, s) => {
            const scores = Object.values(s.career_predictions || {});
            return acc + (scores.length ? Math.max(...scores) : 0);
        }, 0);
        return Math.round(sum / this.students.length);
    },

    revealBars(student) {
        let stats = student?.exam_stats || [];

        if (this.filterPeriod) {
            stats = stats.filter(item => {
                const itemPeriod = item.period_title || item.exam?.period_title;
                return itemPeriod === this.filterPeriod;
            });
        }

        const groups = {};
        stats.forEach(item => {
            const cat = item.category || 'Umum';
            if (!groups[cat]) groups[cat] = { label: cat, items: [] };
            groups[cat].items.push({
                exam_id: item.exam_id,
                title: item.title || item.exam_title || '-',
                subcategory: item.subcategory || '',
                period_title: item.period_title || item.exam?.period_title || 'PSB',
                value: Number(item.mc_accuracy_pct ?? item.score ?? 0),
                completed: !!item.completed,
                retake_allowed: !!item.retake_allowed,
            });
        });
        return Object.values(groups);
    },

    async allowRetake(student, item) {
        if (!item?.exam_id || item.retake_allowed) return;
        const key = student.id + '-' + item.exam_id;
        this.retakeBusy = key;
        try {
            await api.allowRetake({ user_id: student.id, exam_id: item.exam_id });
            item.retake_allowed = true;
            if (window.notifySuccess) {
                window.notifySuccess(`Izin ulang ujian diberikan kepada ${student.name}!`);
            }
        } catch (e) {
            alert(e.message || 'Gagal memberikan izin ulang');
        } finally {
            this.retakeBusy = null;
        }
    },
}));

// ---------- 8. Admin Koreksi Jawaban ----------
Alpine.data('adminKoreksiPage', () => ({
    loading: true,
    error: '',
    student: null,
    selectedExam: null,
    answers: [],
    scores: {},
    previewModalImg: null,

    async init() {
        const user = getUser();
        if (!user || !['admin', 'teacher'].includes(user.role)) {
            this.error = 'Akses ditolak.';
            this.loading = false;
            return;
        }

        const parts = window.location.pathname.split('/').filter(Boolean);
        const userId = parts[parts.length - 1];
        const urlParams = new URLSearchParams(window.location.search);
        const examId = urlParams.get('exam_id');

        try {
            let endpoint = `/admin/students/${userId}/answers`;
            if (examId) {
                endpoint += `?exam_id=${examId}`;
            }

            const token = localStorage.getItem('ts_token') || localStorage.getItem('token');
            const res = await fetch(`/api${endpoint}`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });
            const json = await res.json();
            const data = json?.data ?? {};

            this.student = data.student ?? null;
            this.selectedExam = data.selected_exam ?? null;
            this.answers = data.answers ?? [];
            this.answers.forEach((a) => {
                if (a.current_score != null) this.scores[a.answer_id] = a.current_score;
            });
        } catch (e) {
            this.error = e.message;
        } finally {
            this.loading = false;
        }
    },

    async saveScore(answerId) {
        const score = this.scores[answerId];
        if (score === undefined || score === '' || isNaN(score)) {
            alert('Masukkan angka nilai valid antara 0–100');
            return;
        }

        try {
            await api.gradeAnswer({ answer_id: answerId, score: parseFloat(score) });
            const answer = this.answers.find((a) => a.answer_id === answerId);
            if (answer) answer.current_score = parseFloat(score);
            if (window.notifySuccess) {
                window.notifySuccess('Nilai koreksi berhasil disimpan!');
            }
        } catch (e) {
            alert(e.message || 'Gagal menyimpan nilai');
        }
    },
}));

Alpine.start();