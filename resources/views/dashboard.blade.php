<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Kalkulator HPP & P/L Stok Voucher
        </h2>
    </x-slot>

    <!-- Tailwind CDN: dipakai agar semua utility class (termasuk warna/ukuran arbitrary
         seperti bg-[#6c7dfb], w-[380px]) ter-render persis seperti file HTML asli,
         tanpa tergantung proses build Vite yang bisa memangkas class yang jarang dipakai. -->
    <script src="https://cdn.tailwindcss.com"></script>

    <div id="hpp-root" class="py-4"></div>

    <script>
        window.__INITIAL_CATEGORIES__ = @json($initialCategories);
        window.__INITIAL_CATEGORY_IDS__ = @json($initialCategoryIds);
        window.__INITIAL_TRANSACTIONS__ = @json($initialTransactions);
        window.__CSRF_TOKEN__ = @json(csrf_token());
        window.__ROUTES__ = {
            categoriesStore: @json(route('categories.store')),
            categoriesDestroy: @json(url('/categories')),
            transactionsStore: @json(route('transactions.store')),
            transactionsDestroy: @json(url('/transactions')),
            transactionsClear: @json(route('transactions.clear')),
        };
    </script>

    <!-- React & ReactDOM dari CDN -->
    <script src="https://unpkg.com/react@18/umd/react.development.js" crossorigin></script>
    <script src="https://unpkg.com/react-dom@18/umd/react-dom.development.js" crossorigin></script>

    <!-- Babel untuk kompilasi JSX di browser -->
    <script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>

    <script type="text/babel">
        const { useState, useMemo } = React;

        // --- Komponen Ikon SVG ---
        const TrendingUp = ({ className }) => <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" className={className}><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline><polyline points="16 7 22 7 22 13"></polyline></svg>;
        const PlusCircle = ({ className }) => <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" className={className}><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>;
        const Trash2 = ({ className }) => <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" className={className}><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>;
        const Wallet = ({ className }) => <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" className={className}><path d="M21 12V7H5a2 2 0 0 1 0-4h14v4"></path><path d="M3 5v14a2 2 0 0 0 2 2h16v-5"></path><path d="M18 12a2 2 0 0 0 0 4h4v-4Z"></path></svg>;
        const ListIcon = ({ className }) => <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" className={className}><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>;
        const PieChart = ({ className }) => <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" className={className}><path d="M21.21 15.89A10 10 0 1 1 8 2.83"></path><path d="M22 12A10 10 0 0 0 12 2v10z"></path></svg>;
        const ArrowUpRight = ({ className }) => <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" className={className}><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>;
        const ArrowDownRight = ({ className }) => <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" className={className}><line x1="7" y1="7" x2="17" y2="17"></line><polyline points="17 7 17 17 7 17"></polyline></svg>;
        const AlertCircle = ({ className }) => <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" className={className}><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>;
        const TagIcon = ({ className }) => <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" className={className}><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg>;
        const XIcon = ({ className }) => <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" className={className}><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>;

        // --- Helper untuk panggil API Laravel ---
        async function apiFetch(url, options = {}) {
            const res = await fetch(url, {
                ...options,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': window.__CSRF_TOKEN__,
                    ...(options.headers || {}),
                },
            });

            let data = null;
            try { data = await res.json(); } catch (e) { data = null; }

            if (!res.ok) {
                const message = (data && data.message) ? data.message : 'Terjadi kesalahan pada server.';
                throw new Error(message);
            }
            return data;
        }

        function App() {
            const [transactions, setTransactions] = useState(window.__INITIAL_TRANSACTIONS__ || []);
            const [categories, setCategories] = useState(window.__INITIAL_CATEGORIES__ || []);
            const [categoryIds, setCategoryIds] = useState(window.__INITIAL_CATEGORY_IDS__ || {});
            const [newCategory, setNewCategory] = useState('');
            const [error, setError] = useState('');
            const [isSubmitting, setIsSubmitting] = useState(false);

            const [deleteTarget, setDeleteTarget] = useState({ type: null, payload: null });
            const [showSafePopup, setShowSafePopup] = useState(false);
            const [showSuccessToast, setShowSuccessToast] = useState(false);

            const [formData, setFormData] = useState({
                type: 'BUY',
                voucherCode: (window.__INITIAL_CATEGORIES__ && window.__INITIAL_CATEGORIES__[0]) || '',
                price: '',
                quantity: '',
            });

            const formatRupiah = (number) => {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                }).format(number);
            };

            const formatIndonesianDate = (date) => {
                const days = ['minggu', 'senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'];
                const months = ['januari', 'februari', 'maret', 'april', 'mei', 'juni', 'juli', 'agustus', 'september', 'oktober', 'november', 'desember'];

                const dayName = days[date.getDay()];
                const day = date.getDate();
                const monthName = months[date.getMonth()];
                const year = date.getFullYear();

                const hours = String(date.getHours()).padStart(2, '0');
                const minutes = String(date.getMinutes()).padStart(2, '0');

                return `${dayName}, ${day} ${monthName} ${year} (${hours}:${minutes} WIB)`;
            };

            const handleInputChange = (e) => {
                const { name, value } = e.target;
                setFormData({ ...formData, [name]: value });
                setError('');
            };

            const handleAddCategory = async (e) => {
                e.preventDefault();
                const trimmed = newCategory.trim().toUpperCase();
                if (!trimmed) return;

                if (categories.includes(trimmed)) {
                    setError(`Gagal: Kategori "${trimmed}" sudah terdaftar.`);
                    return;
                }

                try {
                    const created = await apiFetch(window.__ROUTES__.categoriesStore, {
                        method: 'POST',
                        body: JSON.stringify({ name: trimmed }),
                    });

                    setCategories([...categories, created.name]);
                    setCategoryIds({ ...categoryIds, [created.name]: created.id });
                    setNewCategory('');
                    setError('');
                    setFormData(prev => ({ ...prev, voucherCode: created.name }));
                } catch (err) {
                    setError(err.message);
                }
            };

            const initiateDeleteCategory = (catToDelete) => {
                setError('');
                setShowSuccessToast(false);
                setDeleteTarget({ type: 'CATEGORY', payload: catToDelete });
            };

            const initiateDeleteTransaction = (id) => {
                setError('');
                setShowSuccessToast(false);
                setDeleteTarget({ type: 'TRANSACTION', payload: id });
            };

            const initiateClearAllHistory = () => {
                setError('');
                setShowSuccessToast(false);
                setDeleteTarget({ type: 'CLEAR_HISTORY', payload: 'ALL' });
            };

            const handleCancelDelete = () => {
                setDeleteTarget({ type: null, payload: null });
                setShowSafePopup(true);
            };

            const handleConfirmDelete = async () => {
                try {
                    if (deleteTarget.type === 'CATEGORY') {
                        const catId = categoryIds[deleteTarget.payload];
                        await apiFetch(`${window.__ROUTES__.categoriesDestroy}/${catId}`, { method: 'DELETE' });

                        const updatedCats = categories.filter(c => c !== deleteTarget.payload);
                        setCategories(updatedCats);

                        const updatedIds = { ...categoryIds };
                        delete updatedIds[deleteTarget.payload];
                        setCategoryIds(updatedIds);

                        setTransactions(transactions.filter(t => t.voucherCode !== deleteTarget.payload));

                        if (formData.voucherCode === deleteTarget.payload) {
                            setFormData(prev => ({ ...prev, voucherCode: updatedCats[0] || '' }));
                        }
                    } else if (deleteTarget.type === 'TRANSACTION') {
                        await apiFetch(`${window.__ROUTES__.transactionsDestroy}/${deleteTarget.payload}`, { method: 'DELETE' });
                        setTransactions(transactions.filter(t => t.id !== deleteTarget.payload));
                    } else if (deleteTarget.type === 'CLEAR_HISTORY') {
                        await apiFetch(window.__ROUTES__.transactionsClear, { method: 'DELETE' });
                        setTransactions([]);
                    }

                    setDeleteTarget({ type: null, payload: null });
                    setShowSuccessToast(true);
                } catch (err) {
                    setDeleteTarget({ type: null, payload: null });
                    setError(err.message);
                }
            };

            const handleSubmitTransaction = async (e) => {
                e.preventDefault();
                if (!formData.voucherCode || !formData.price || !formData.quantity) {
                    setError('Mohon pilih voucher dan isi harga serta jumlah lembar.');
                    return;
                }

                const qty = parseInt(formData.quantity, 10);
                const price = parseFloat(formData.price);
                const categoryId = categoryIds[formData.voucherCode];

                setIsSubmitting(true);
                try {
                    const created = await apiFetch(window.__ROUTES__.transactionsStore, {
                        method: 'POST',
                        body: JSON.stringify({
                            category_id: categoryId,
                            type: formData.type,
                            price: price,
                            quantity: qty,
                        }),
                    });

                    setTransactions([...transactions, created]);
                    setShowSuccessToast(false);

                    setFormData(prev => ({
                        ...prev,
                        price: '',
                        quantity: '',
                    }));
                    setError('');
                } catch (err) {
                    setError(err.message);
                } finally {
                    setIsSubmitting(false);
                }
            };

            const { summary, history } = useMemo(() => {
                const recap = {};
                const enrichedHistory = [];

                transactions.forEach((t) => {
                    if (!recap[t.voucherCode]) {
                        recap[t.voucherCode] = {
                            voucherCode: t.voucherCode,
                            currentQty: 0,
                            currentCapital: 0,
                            currentHPP: 0,
                            realizedPnL: 0,
                        };
                    }

                    const stock = recap[t.voucherCode];
                    let rowPnL = 0;

                    if (t.type === 'BUY') {
                        stock.currentQty += t.quantity;
                        stock.currentCapital += t.total;
                        stock.currentHPP = stock.currentQty > 0 ? stock.currentCapital / stock.currentQty : 0;
                    } else if (t.type === 'SELL') {
                        rowPnL = (t.price - stock.currentHPP) * t.quantity;
                        stock.realizedPnL += rowPnL;

                        stock.currentQty -= t.quantity;
                        stock.currentCapital = stock.currentQty * stock.currentHPP;
                    }

                    enrichedHistory.push({
                        ...t,
                        currentHPP: stock.currentHPP,
                        rowPnL: t.type === 'SELL' ? rowPnL : null,
                    });
                });

                return {
                    summary: Object.values(recap).filter(item => item.currentQty > 0 || item.realizedPnL !== 0),
                    history: enrichedHistory.reverse()
                };
            }, [transactions]);

            const liveEstimation = useMemo(() => {
                if (!formData.voucherCode || !formData.price || !formData.quantity) return null;

                const qty = parseInt(formData.quantity, 10) || 0;
                const price = parseFloat(formData.price) || 0;
                if (qty <= 0 || price <= 0) return null;

                const currentStock = summary.find(s => s.voucherCode === formData.voucherCode) || {
                    currentQty: 0,
                    currentCapital: 0,
                    currentHPP: 0
                };

                let adjustedQty = currentStock.currentQty;
                let adjustedHPP = currentStock.currentHPP;
                let adjustedCapital = currentStock.currentCapital;
                let estimatedPnL = 0;

                if (formData.type === 'BUY') {
                    adjustedQty += qty;
                    adjustedCapital += (price * qty);
                    adjustedHPP = adjustedQty > 0 ? adjustedCapital / adjustedQty : 0;
                } else if (formData.type === 'SELL') {
                    if (qty > currentStock.currentQty) {
                        return { error: true, message: 'Jumlah jual melebihi sisa voucher!' };
                    }
                    estimatedPnL = (price - currentStock.currentHPP) * qty;
                    adjustedQty -= qty;
                    adjustedHPP = currentStock.currentHPP;
                    adjustedCapital = adjustedQty * adjustedHPP;
                }

                return {
                    error: false,
                    oldHPP: currentStock.currentHPP,
                    adjustedHPP,
                    adjustedQty,
                    estimatedPnL,
                    type: formData.type
                };
            }, [formData, summary]);

            const totalPortfolioValue = summary.reduce((acc, curr) => acc + curr.currentCapital, 0);
            const totalRealizedPnL = summary.reduce((acc, curr) => acc + curr.realizedPnL, 0);

            return (
                <div className="min-h-screen bg-gray-50 text-gray-800 p-4 md:p-8 font-sans">

                {deleteTarget.type && (
                    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
                        <div className="bg-white rounded-xl p-8 w-[380px] text-center shadow-2xl scale-100 transition-transform">
                            <div className="mx-auto flex items-center justify-center h-20 w-20 rounded-full border-[3px] border-[#f8cba6] mb-6 relative">
                                <span className="text-5xl text-[#f39c12] font-semibold absolute -top-1">!</span>
                            </div>
                            <h3 className="text-2xl font-normal text-gray-600 mb-3">Anda Yakin?</h3>
                            <p className="text-gray-500 text-sm mb-8 px-2 leading-relaxed">
                                {deleteTarget.type === 'CATEGORY'
                                    ? 'Tindakan ini akan menghapus seluruh kategori voucher pada kolom "Riwayat Log Transaksi" dan kolom "Rekapan Ringkasan Stok Voucher"'
                                    : deleteTarget.type === 'CLEAR_HISTORY'
                                    ? 'Tindakan ini akan menghapus seluruh Riwayat Log Transaksi pada kolom "Riwayat Log Transaksi" dan bersifat permanen (tidak dapat dipulihkan)'
                                    : 'Data yang dihapus tidak dapat dikembalikan!'}
                            </p>
                            <div className="flex justify-center gap-3">
                                <button onClick={handleCancelDelete} className="px-5 py-2.5 bg-gray-100 text-gray-500 text-sm rounded shadow hover:bg-gray-200 font-medium transition-colors">Cancel</button>
                                <button onClick={handleConfirmDelete} className="px-7 py-2.5 bg-[#6c7dfb] text-white text-sm rounded shadow hover:bg-[#5b6be6] font-medium transition-colors">OK</button>
                            </div>
                        </div>
                    </div>
                )}

                {showSafePopup && (
                    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
                        <div className="bg-white rounded-xl p-8 w-80 text-center shadow-2xl scale-100 transition-transform">
                            <p className="text-gray-500 font-normal text-lg mb-8 mt-2">Data Anda aman!</p>
                            <button onClick={() => setShowSafePopup(false)} className="px-8 py-2.5 bg-[#6c7dfb] text-white text-sm rounded shadow hover:bg-[#5b6be6] font-medium transition-colors">OK</button>
                        </div>
                    </div>
                )}

                <div className="max-w-7xl mx-auto space-y-6">

                    <header className="bg-white rounded-2xl shadow-sm p-6 border border-gray-100 flex flex-col md:flex-row items-center justify-between gap-6">
                        <div>
                            <h1 className="text-2xl font-bold text-indigo-700 flex items-center gap-2">
                            <TrendingUp className="w-7 h-7" />
                            Kalkulator HPP & P/L Stok Voucher
                            </h1>
                            <p className="text-gray-500 text-sm mt-1">Kelola kategori voucher, hitung HPP rata-rata (Average Cost) & pantau Untung/Rugi realisasi</p>
                        </div>
                        <div className="flex gap-4 w-full md:w-auto">
                            <div className="bg-indigo-50 p-4 rounded-xl flex-1 md:min-w-[200px]">
                            <p className="text-sm text-indigo-600 font-semibold mb-1">Total Nilai Stok Aktif</p>
                            <p className="text-xl font-bold text-indigo-900">{formatRupiah(totalPortfolioValue)}</p>
                            </div>
                            <div className={`${totalRealizedPnL >= 0 ? 'bg-green-50' : 'bg-red-50'} p-4 rounded-xl flex-1 md:min-w-[200px]`}>
                            <p className={`text-sm font-semibold mb-1 ${totalRealizedPnL >= 0 ? 'text-green-700' : 'text-red-700'}`}>
                                Total Untung/Rugi (Realized)
                            </p>
                            <p className={`text-xl font-bold ${totalRealizedPnL >= 0 ? 'text-green-700' : 'text-red-700'}`}>
                                {totalRealizedPnL > 0 ? '+' : ''}{formatRupiah(totalRealizedPnL)}
                            </p>
                            </div>
                        </div>
                    </header>

                    {showSuccessToast && (
                        <div className="bg-[#68d391] text-white px-5 py-3.5 rounded-lg shadow-sm flex items-center justify-between animate-fade-in-down">
                            <span className="text-sm font-medium">Data stok berhasil dihapus.</span>
                            <button onClick={() => setShowSuccessToast(false)} className="text-white hover:text-green-100 opacity-80 hover:opacity-100 transition-opacity">
                                <XIcon className="w-5 h-5" />
                            </button>
                        </div>
                    )}

                    {error && (
                        <div className="p-4 bg-red-50 border border-red-200 rounded-2xl flex items-start gap-2 text-red-600 text-sm shadow-sm">
                            <AlertCircle className="w-5 h-5 shrink-0 mt-0.5" />
                            <p className="font-medium">{error}</p>
                        </div>
                    )}

                    <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div className="lg:col-span-1 space-y-6">
                            <div className="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                                <h2 className="text-lg font-bold mb-4 flex items-center gap-2 text-gray-800">
                                    <TagIcon className="w-5 h-5 text-indigo-500" />
                                    1. Kelola Kategori Voucher
                                </h2>

                                <form onSubmit={handleAddCategory} className="flex gap-2 mb-4">
                                    <input
                                        type="text"
                                        value={newCategory}
                                        onChange={(e) => setNewCategory(e.target.value)}
                                        placeholder="Contoh: VOUCHER MAP"
                                        className="flex-1 px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-sm transition-all"
                                    />
                                    <button
                                        type="submit"
                                        className="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-xl shadow-sm transition-colors"
                                    >
                                        Tambah
                                    </button>
                                </form>

                                <div className="border border-gray-100 rounded-xl overflow-hidden max-h-48 overflow-y-auto">
                                    <table className="w-full text-left border-collapse text-xs">
                                        <thead>
                                            <tr className="bg-gray-50 text-gray-500 uppercase tracking-wider border-b border-gray-200">
                                                <th className="p-2 font-semibold pl-3">Daftar Voucher</th>
                                                <th className="p-2 font-semibold text-center w-12">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {categories.length === 0 ? (
                                                <tr>
                                                    <td colSpan="2" className="text-center py-4 text-gray-400 italic">Belum ada kategori voucher.</td>
                                                </tr>
                                            ) : (
                                                categories.map((cat, index) => (
                                                    <tr key={index} className="border-b border-gray-100 hover:bg-gray-50">
                                                        <td className="p-2 font-bold text-gray-700 pl-3">{cat}</td>
                                                        <td className="p-2 text-center">
                                                            <button
                                                                type="button"
                                                                onClick={() => initiateDeleteCategory(cat)}
                                                                className="text-gray-400 hover:text-red-600 p-1 rounded transition-colors"
                                                                title="Hapus Kategori"
                                                            >
                                                                <Trash2 className="w-4 h-4 mx-auto" />
                                                            </button>
                                                        </td>
                                                    </tr>
                                                ))
                                            )}
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div className="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                                <h2 className="text-lg font-bold mb-4 flex items-center gap-2 text-gray-800">
                                    <PlusCircle className="w-5 h-5 text-indigo-500" />
                                    2. Input Transaksi
                                </h2>

                                <form onSubmit={handleSubmitTransaction} className="space-y-4">
                                    <div className="flex bg-gray-100 p-1 rounded-xl">
                                        <button
                                            type="button"
                                            onClick={() => setFormData({...formData, type: 'BUY'})}
                                            className={`flex-1 py-2 text-sm font-semibold rounded-lg transition-colors ${formData.type === 'BUY' ? 'bg-white shadow-sm text-blue-600' : 'text-gray-500 hover:text-gray-700'}`}
                                        >
                                            Beli / Restock
                                        </button>
                                        <button
                                            type="button"
                                            onClick={() => setFormData({...formData, type: 'SELL'})}
                                            className={`flex-1 py-2 text-sm font-semibold rounded-lg transition-colors ${formData.type === 'SELL' ? 'bg-white shadow-sm text-orange-600' : 'text-gray-500 hover:text-gray-700'}`}
                                        >
                                            Jual Voucher
                                        </button>
                                    </div>

                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-1">Pilih Voucher</label>
                                        <select
                                            name="voucherCode"
                                            value={formData.voucherCode}
                                            onChange={handleInputChange}
                                            className="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-sm transition-all bg-white"
                                            required
                                        >
                                            <option value="" disabled>-- Pilih Kategori --</option>
                                            {categories.map((cat, index) => (
                                                <option key={index} value={cat}>{cat}</option>
                                            ))}
                                        </select>
                                    </div>

                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-1">Harga per Lembar (Rp)</label>
                                        <input
                                            type="number"
                                            name="price"
                                            value={formData.price}
                                            onChange={handleInputChange}
                                            placeholder="Contoh: 95000"
                                            min="1"
                                            className="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all"
                                            required
                                        />
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-1">Jumlah (Lembar / Pcs)</label>
                                        <input
                                            type="number"
                                            name="quantity"
                                            value={formData.quantity}
                                            onChange={handleInputChange}
                                            placeholder="Contoh: 10"
                                            min="1"
                                            className="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all"
                                            required
                                        />
                                    </div>

                                    {liveEstimation && !liveEstimation.error && (
                                        <div className="p-4 bg-indigo-50 border border-indigo-200 rounded-xl space-y-2 text-xs">
                                            <p className="font-bold text-indigo-800 text-sm flex items-center gap-1">
                                                <TrendingUp className="w-4 h-4" /> Live Preview Adjustment
                                            </p>
                                            <div className="flex justify-between border-b border-indigo-100 pb-1">
                                                <span className="text-gray-600">HPP Saat Ini:</span>
                                                <span className="font-semibold text-gray-800">{formatRupiah(liveEstimation.oldHPP)}</span>
                                            </div>
                                            <div className="flex justify-between border-b border-indigo-100 pb-1">
                                                <span className="text-indigo-700 font-medium">HPP Terkini (Adjusted):</span>
                                                <span className="font-bold text-indigo-700 bg-indigo-100 px-1.5 rounded">{formatRupiah(liveEstimation.adjustedHPP)}</span>
                                            </div>
                                            <div className="flex justify-between">
                                                <span className="text-gray-600">Sisa Stok Final:</span>
                                                <span className="font-semibold text-gray-800">{liveEstimation.adjustedQty.toLocaleString('id-ID')} Pcs</span>
                                            </div>
                                            {liveEstimation.type === 'SELL' && (
                                                <div className="flex justify-between border-t border-indigo-200 pt-1 mt-1">
                                                    <span className="text-gray-600 font-medium">Estimasi P/L Realisasi:</span>
                                                    <span className={`font-bold ${liveEstimation.estimatedPnL >= 0 ? 'text-green-600' : 'text-red-600'}`}>
                                                        {liveEstimation.estimatedPnL >= 0 ? '+' : ''}{formatRupiah(liveEstimation.estimatedPnL)}
                                                    </span>
                                                </div>
                                            )}
                                        </div>
                                    )}

                                    <button
                                        type="submit"
                                        disabled={categories.length === 0 || isSubmitting}
                                        className={`w-full text-white font-medium py-3 rounded-xl transition-colors shadow-sm flex justify-center items-center gap-2 ${
                                            categories.length === 0 || isSubmitting ? 'bg-gray-400 cursor-not-allowed' :
                                            formData.type === 'BUY' ? 'bg-blue-600 hover:bg-blue-700' : 'bg-orange-600 hover:bg-orange-700'
                                        }`}
                                    >
                                        <PlusCircle className="w-5 h-5" />
                                        {isSubmitting ? 'Menyimpan...' : (formData.type === 'BUY' ? 'Simpan Transaksi Beli' : 'Simpan Transaksi Jual')}
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div className="lg:col-span-2 space-y-6">
                            <div className="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                                <h2 className="text-lg font-bold mb-4 flex items-center gap-2 text-gray-800">
                                    <PieChart className="w-5 h-5 text-indigo-500" />
                                    Rekapan Ringkasan Stok Voucher
                                </h2>

                                {summary.length === 0 ? (
                                    <div className="text-center py-8 text-gray-400 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                                        <Wallet className="w-10 h-10 mx-auto mb-2 opacity-50" />
                                        <p>Belum ada rekapan stok. Lakukan transaksi pembelian terlebih dahulu.</p>
                                    </div>
                                ) : (
                                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        {summary.map((item) => (
                                            <div key={item.voucherCode} className="border border-gray-200 rounded-xl p-4 shadow-sm relative overflow-hidden bg-white">
                                                {item.currentQty === 0 && (
                                                    <div className="absolute top-0 right-0 bg-gray-200 text-gray-600 text-xs px-2 py-1 rounded-bl-lg font-medium">
                                                        STOK HABIS
                                                    </div>
                                                )}

                                                <div className="flex justify-between items-start mb-4">
                                                    <span className="text-xl font-extrabold text-indigo-900 tracking-tight break-all mr-2">{item.voucherCode}</span>
                                                    <span className="bg-gray-100 text-gray-800 text-xs font-semibold px-2.5 py-1 rounded-full border border-gray-200 shrink-0">
                                                        {item.currentQty.toLocaleString('id-ID')} Pcs
                                                    </span>
                                                </div>

                                                <div className="space-y-2">
                                                    <div className="flex justify-between text-sm bg-gray-50 p-2 rounded-lg">
                                                        <span className="text-gray-500">HPP Final (Adjusted):</span>
                                                        <span className="font-bold text-indigo-600">{formatRupiah(item.currentHPP)}</span>
                                                    </div>
                                                    <div className="flex justify-between text-sm bg-gray-50 p-2 rounded-lg">
                                                        <span className="text-gray-500">Nilai Stok Aktif:</span>
                                                        <span className="font-semibold text-gray-800">{formatRupiah(item.currentCapital)}</span>
                                                    </div>
                                                    <div className="flex justify-between text-sm p-2 rounded-lg border-t border-gray-100 mt-2">
                                                        <span className="text-gray-500">Realized P/L (Untung/Rugi):</span>
                                                        <span className={`font-bold ${item.realizedPnL > 0 ? 'text-green-600' : item.realizedPnL < 0 ? 'text-red-600' : 'text-gray-600'}`}>
                                                            {item.realizedPnL > 0 ? '+' : ''}{formatRupiah(item.realizedPnL)}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                )}
                            </div>

                            <div className="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 overflow-hidden">
                                <div className="flex items-center justify-between mb-4">
                                    <h2 className="text-lg font-bold flex items-center gap-2 text-gray-800">
                                        <ListIcon className="w-5 h-5 text-indigo-500" />
                                        Riwayat Log Transaksi
                                    </h2>
                                    <button
                                        type="button"
                                        onClick={initiateClearAllHistory}
                                        className="bg-[#6c7dfb] hover:bg-[#5b6be6] text-white text-sm font-semibold px-4 py-2 rounded-xl shadow-sm transition-colors"
                                    >
                                        Clear All Data
                                    </button>
                                </div>

                                <div className="overflow-x-auto">
                                    <table className="w-full text-left border-collapse whitespace-nowrap">
                                        <thead>
                                            <tr className="bg-gray-50 text-gray-600 text-xs uppercase tracking-wider border-b border-gray-200">
                                                <th className="p-3 font-semibold rounded-tl-lg">Tipe</th>
                                                <th className="p-3 font-semibold">Nama Voucher</th>
                                                <th className="p-3 font-semibold">Harga Unit</th>
                                                <th className="p-3 font-semibold">Qty</th>
                                                <th className="p-3 font-semibold text-indigo-700">HPP Rata-Rata</th>
                                                <th className="p-3 font-semibold">Realized P/L</th>
                                                <th className="p-3 font-semibold text-indigo-900">Waktu Transaksi</th>
                                                <th className="p-3 font-semibold text-center rounded-tr-lg">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody className="text-sm">
                                            {history.length === 0 ? (
                                                <tr>
                                                    <td colSpan="8" className="text-center py-6 text-gray-400">
                                                        Belum ada log transaksi tercatat.
                                                    </td>
                                                </tr>
                                            ) : (
                                                history.map((t) => (
                                                    <tr key={t.id} className="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                                        <td className="p-3">
                                                            {t.type === 'BUY' ? (
                                                                <span className="flex items-center gap-1 text-blue-600 bg-blue-50 px-2 py-1 rounded-md text-xs font-bold w-max">
                                                                    <ArrowDownRight className="w-4 h-4" /> Beli
                                                                </span>
                                                            ) : (
                                                                <span className="flex items-center gap-1 text-orange-600 bg-orange-50 px-2 py-1 rounded-md text-xs font-bold w-max">
                                                                    <ArrowUpRight className="w-4 h-4" /> Jual
                                                                </span>
                                                            )}
                                                        </td>
                                                        <td className="p-3 font-bold text-gray-800">{t.voucherCode}</td>
                                                        <td className="p-3">{formatRupiah(t.price)}</td>
                                                        <td className="p-3">{t.quantity.toLocaleString('id-ID')}</td>
                                                        <td className="p-3 font-bold text-indigo-600 bg-indigo-50/50">{formatRupiah(t.currentHPP)}</td>
                                                        <td className="p-3 font-medium">
                                                            {t.type === 'SELL' ? (
                                                                <span className={t.rowPnL > 0 ? 'text-green-600' : t.rowPnL < 0 ? 'text-red-600' : 'text-gray-500'}>
                                                                    {t.rowPnL > 0 ? '+' : ''}{formatRupiah(t.rowPnL)}
                                                                </span>
                                                            ) : (
                                                                <span className="text-gray-300">-</span>
                                                            )}
                                                        </td>
                                                        <td className="p-3 text-xs text-gray-500 font-mono font-medium">
                                                            {formatIndonesianDate(new Date(t.date))}
                                                        </td>
                                                        <td className="p-3 text-center">
                                                            <button
                                                                onClick={() => initiateDeleteTransaction(t.id)}
                                                                className="text-gray-400 hover:text-red-600 hover:bg-red-50 p-2 rounded-lg transition-colors"
                                                                title="Hapus Transaksi"
                                                            >
                                                                <Trash2 className="w-4 h-4 mx-auto" />
                                                            </button>
                                                        </td>
                                                    </tr>
                                                ))
                                            )}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
            );
        }

        const root = ReactDOM.createRoot(document.getElementById('hpp-root'));
        root.render(<App />);
    </script>
</x-app-layout>
