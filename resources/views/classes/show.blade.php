<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
            
            <h2 class="font-semibold text-2xl dark:bg-gray-900 rounded-lg px-4 py-2 text-gray-900 dark:text-gray-100 transition-colors duration-300 sm:w-auto text-center sm:text-left">
                Class Details
            </h2>

           
        </div>
    </x-slot>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="py-6 px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto">
            <div class="bg-white dark:bg-gray-900 shadow rounded-lg p-6 text-gray-900 dark:text-gray-100 transition">

                <!-- Class Info -->
                <h3 class="text-lg font-semibold mb-4 border-l-4 border-blue-500 pl-2">Class Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div><strong>Class Name:</strong> {{ $class->name }}</div>
                    <div><strong>Section:</strong> {{ $class->section ?? '-' }}</div>
                    <div><strong>Assigned Teacher:</strong> {{ $class->formTeacher->user->name ?? 'Unassigned' }}</div>
                    <div></div>
                </div>

                <!-- Controls: Filter + (mobile summary) -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
                    <div class="flex items-center gap-3">
                        <label for="filter" class="text-sm font-medium">Filter:</label>
                        <select id="filter" class="px-3 py-1 border rounded dark:bg-gray-700 dark:text-gray-100">
                            <option value="all">All Students</option>
                            <option value="unpaid">Owing</option>
                            <option value="paid">Fully Paid</option>
                        </select>
                    </div>

                    <div class="text-sm text-gray-600 dark:text-gray-300">
                        <!-- Mobile summary -->
                        <span class="hidden md:inline">Showing students in this class.</span>
                    </div>
                </div>

                @php
                    // server-side amounts used to seed data attributes
                    $latestFee = \App\Models\Fee::where('class_id', $class->id)->orderByDesc('id')->value('amount') ?? 0;
                    $allPayments = collect();
                    foreach ($class->students as $s) { $allPayments = $allPayments->merge($s->feePayments); }
                    $latestPaymentDate = $allPayments->max('created_at');
                @endphp

                @if($class->students->isEmpty())
                    <p class="text-gray-500 dark:text-gray-400">No students enrolled in this class.</p>
                @else
                    <!-- Hidden data template: each child div holds dataset for each student -->
                    <div id="students-data" class="hidden">
                        @foreach($class->students as $student)
                            @php
                                $totalPaid = $student->feePayments->sum('amount');
                                $balance = max($latestFee - $totalPaid, 0);
                                $lastPayment = $student->feePayments->sortByDesc('created_at')->first();
                                $lastDate = $lastPayment ? $lastPayment->created_at->format('Y-m-d') : null;
                            @endphp

                            <div
                                class="student-item"
                                data-id="{{ $student->id }}"
                                data-name="{{ e($student->name) }}"
                                data-adm="{{ e($student->admission_number) }}"
                                data-fee="{{ $latestFee }}"
                                data-paid="{{ $totalPaid }}"
                                data-balance="{{ $balance }}"
                                data-last="{{ $lastDate ?? '' }}"
                                data-view="{{ route('students.show', $student->id) }}"
                                data-edit="{{ route('students.edit', $student->id) }}"
                                data-delete="{{ route('students.destroy', $student->id) }}"
                            ></div>
                        @endforeach
                    </div>

                    <!-- Desktop / Tablet Table -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="min-w-full border border-gray-200 dark:border-gray-700 text-sm" id="students-table">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-200">
                                    <th class="px-4 py-2 text-left cursor-pointer sort-header" data-sort="name">Student Name ⬍</th>
                                    <th class="px-4 py-2 text-left">Admission No</th>
                                    <th class="px-4 py-2 text-left cursor-pointer sort-header" data-sort="fee">Total Fee (₦) ⬍</th>
                                    <th class="px-4 py-2 text-left cursor-pointer sort-header" data-sort="paid">Total Paid (₦) ⬍</th>
                                    <th class="px-4 py-2 text-left cursor-pointer sort-header" data-sort="balance">Balance (₦) ⬍</th>
                                    <th class="px-4 py-2 text-left">Last Payment</th>
                                </tr>
                            </thead>
                            <tbody id="students-tbody">
                                {{-- Filled by JS --}}
                            </tbody>

                            <tfoot class="bg-gray-100 dark:bg-gray-800 font-semibold">
                                <tr>
                                    <td colspan="2" class="px-4 py-2 text-right">Class Totals:</td>
                                    <td class="px-4 py-2" id="total-fee-col">₦0.00</td>
                                    <td class="px-4 py-2 text-green-600 dark:text-green-400" id="total-paid-col">₦0.00</td>
                                    <td class="px-4 py-2 text-red-600 dark:text-red-400" id="total-balance-col">₦0.00</td>
                                    <td class="px-4 py-2"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Mobile Card View -->
                    <div id="students-cards" class="md:hidden space-y-4">
                        {{-- Filled by JS --}}
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4 flex justify-center items-center gap-2" id="pagination">
                        <button id="prevPage" class="px-3 py-1 bg-gray-200 dark:bg-gray-700 rounded disabled:opacity-50">Previous</button>
                        <div id="pageNumbers" class="flex gap-1"></div>
                        <button id="nextPage" class="px-3 py-1 bg-gray-200 dark:bg-gray-700 rounded disabled:opacity-50">Next</button>
                    </div>
                @endif

                <!-- Actions -->
                <div class="mt-6 flex flex-wrap gap-3">
                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('classes.edit', $class->id) }}" class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700">
                            Edit
                        </a>
                        <form action="{{ route('classes.destroy', $class->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
                                onclick="return confirm('Are you sure you want to delete this class?')">
                                Delete
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('classes.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                        Back to List
                    </a>
                </div>

            </div>
        </div>
    </div>

    <!-- JS: client-side sort / filter / paginate + render for table & blue-accent cards on mobile -->
    <script>
        (function () {
            const htmlEl = document.documentElement;
            const toggleBtn = document.getElementById('toggle-dark');
            if (localStorage.getItem('dark-mode') === 'true') htmlEl.classList.add('dark');
            toggleBtn.addEventListener('click', () => {
                htmlEl.classList.toggle('dark');
                localStorage.setItem('dark-mode', htmlEl.classList.contains('dark'));
            });

            // Read data
            const dataNodes = Array.from(document.querySelectorAll('#students-data .student-item'));
            const students = dataNodes.map(n => ({
                id: n.dataset.id,
                name: n.dataset.name || '',
                adm: n.dataset.adm || '',
                fee: parseFloat(n.dataset.fee || 0),
                paid: parseFloat(n.dataset.paid || 0),
                balance: parseFloat(n.dataset.balance || 0),
                last: n.dataset.last || '',
                view: n.dataset.view || '#',
                edit: n.dataset.edit || '#',
                delete: n.dataset.delete || '#',
            }));

            // UI containers
            const tbody = document.getElementById('students-tbody');
            const cardsContainer = document.getElementById('students-cards');
            const filterSelect = document.getElementById('filter');
            const prevBtn = document.getElementById('prevPage');
            const nextBtn = document.getElementById('nextPage');
            const pageNumbers = document.getElementById('pageNumbers');
            const rowsPerPage = 8;
            let currentPage = 1;
            let currentSort = { key: 'name', dir: 'asc' };

            // Sorting UI headers
            document.querySelectorAll('.sort-header').forEach(header => {
                header.addEventListener('click', () => {
                    const key = header.dataset.sort;
                    if (currentSort.key === key) {
                        currentSort.dir = currentSort.dir === 'asc' ? 'desc' : 'asc';
                    } else {
                        currentSort.key = key;
                        currentSort.dir = 'asc';
                    }
                    render();
                });
            });

            // Filter
            filterSelect.addEventListener('change', () => { currentPage = 1; render(); });

            // Pagination
            prevBtn.addEventListener('click', () => {
                if (currentPage > 1) { currentPage--; render(); }
            });
            nextBtn.addEventListener('click', () => {
                const total = getFiltered().length;
                const totalPages = Math.max(1, Math.ceil(total / rowsPerPage));
                if (currentPage < totalPages) { currentPage++; render(); }
            });

            function getFiltered() {
                const filter = filterSelect.value;
                return students.filter(s => {
                    if (filter === 'all') return true;
                    if (filter === 'unpaid') return s.balance > 0;
                    if (filter === 'paid') return s.balance === 0;
                    return true;
                });
            }

            function sortArray(arr) {
                const key = currentSort.key;
                const dir = currentSort.dir === 'asc' ? 1 : -1;
                return arr.slice().sort((a, b) => {
                    if (key === 'name') return dir * a.name.localeCompare(b.name);
                    return dir * (parseFloat(a[key] || 0) - parseFloat(b[key] || 0));
                });
            }

            function renderTable(pageItems, totals) {
                // totals: {fee, paid, balance}
                tbody.innerHTML = pageItems.map(s => `
                    <tr class="border-b dark:border-gray-700">
                        <td class="px-4 py-2">${escapeHtml(s.name)}</td>
                        <td class="px-4 py-2">${escapeHtml(s.adm)}</td>
                        <td class="px-4 py-2">₦${numberFormat(s.fee)}</td>
                        <td class="px-4 py-2 text-green-600 dark:text-green-400">₦${numberFormat(s.paid)}</td>
                        <td class="px-4 py-2 ${s.balance > 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400'}">
                            ₦${numberFormat(s.balance)}
                        </td>
                        <td class="px-4 py-2">${s.last ? escapeHtml(s.last) : '—'}</td>
                    </tr>
                `).join('');
                document.getElementById('total-fee-col').textContent = `₦${numberFormat(totals.fee)}`;
                document.getElementById('total-paid-col').textContent = `₦${numberFormat(totals.paid)}`;
                document.getElementById('total-balance-col').textContent = `₦${numberFormat(totals.balance)}`;
            }

            function renderCards(pageItems, totals) {
                // Blue accent styled cards (mobile)
                cardsContainer.innerHTML = pageItems.map(s => `
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-white dark:bg-gray-800 shadow-sm">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="font-semibold text-blue-700 dark:text-blue-300">${escapeHtml(s.name)}</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-300">Admission: ${escapeHtml(s.adm)}</p>
                            </div>
                            <div class="text-right text-sm">
                                <p class="${s.balance > 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400'} font-semibold">
                                    ${s.balance > 0 ? 'Owing' : 'Paid'}
                                </p>
                            </div>
                        </div>

                        <div class="mt-3 grid grid-cols-3 gap-2 text-xs">
                            <div class="text-gray-500 dark:text-gray-300"><span class="font-medium">Fee</span><br>₦${numberFormat(s.fee)}</div>
                            <div class="text-gray-500 dark:text-gray-300"><span class="font-medium">Paid</span><br><span class="text-green-600 dark:text-green-400">₦${numberFormat(s.paid)}</span></div>
                            <div class="text-gray-500 dark:text-gray-300"><span class="font-medium">Balance</span><br><span class="${s.balance>0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400'}">₦${numberFormat(s.balance)}</span></div>
                        </div>

                        <div class="mt-3 flex items-center justify-between">
                            <div class="text-sm text-gray-500 dark:text-gray-300">
                                Last: ${s.last ? escapeHtml(s.last) : '—'}
                            </div>

                            <div class="flex gap-2">
                                <a href="${s.view}" class="px-3 py-1 bg-blue-600 text-white rounded text-xs">View</a>
                                <a href="${s.edit}" class="px-3 py-1 bg-yellow-500 text-white rounded text-xs">Edit</a>
                                <button data-id="${s.id}" class="px-3 py-1 bg-red-600 text-white rounded text-xs js-delete-btn">Delete</button>
                            </div>
                        </div>
                    </div>
                `).join('');

                // attach delete handlers for card buttons
                Array.from(cardsContainer.querySelectorAll('.js-delete-btn')).forEach(btn => {
                    btn.addEventListener('click', handleDeleteClick);
                });
            }

            // Render pagination buttons
            function renderPagination(totalItems) {
                const totalPages = Math.max(1, Math.ceil(totalItems / rowsPerPage));
                pageNumbers.innerHTML = '';
                for (let i = 1; i <= totalPages; i++) {
                    const b = document.createElement('button');
                    b.textContent = i;
                    b.className = `px-2 py-1 rounded ${i === currentPage ? 'bg-blue-500 text-white' : 'bg-gray-200 dark:bg-gray-700'}`;
                    b.addEventListener('click', () => { currentPage = i; render(); });
                    pageNumbers.appendChild(b);
                }
                prevBtn.disabled = (currentPage <= 1);
                nextBtn.disabled = (currentPage >= totalPages);
            }

            function computeTotals(arr) {
                const totals = { fee: 0, paid: 0, balance: 0 };
                arr.forEach(s => { totals.fee += s.fee; totals.paid += s.paid; totals.balance += s.balance; });
                return totals;
            }

            function render() {
                let list = getFiltered();
                list = sortArray(list);

                // pagination
                const total = list.length;
                const totalPages = Math.max(1, Math.ceil(total / rowsPerPage));
                if (currentPage > totalPages) currentPage = totalPages;
                const start = (currentPage - 1) * rowsPerPage;
                const pageItems = list.slice(start, start + rowsPerPage);

                const totals = computeTotals(list);

                // render both table and cards
                renderTable(pageItems, totals);
                renderCards(pageItems, totals);
                renderPagination(total);
            }

            // helper: delete via form (so CSRF & method work)
            function handleDeleteClick(e) {
                const id = e.currentTarget.dataset.id;
                const item = students.find(s => s.id == id);
                if (!item) return;
                if (!confirm('Are you sure you want to delete this student?')) return;

                // create form and submit
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = item.delete;
                const token = document.querySelector('meta[name="csrf-token"]').content;

                const inputToken = document.createElement('input');
                inputToken.type = 'hidden';
                inputToken.name = '_token';
                inputToken.value = token;
                form.appendChild(inputToken);

                const inputMethod = document.createElement('input');
                inputMethod.type = 'hidden';
                inputMethod.name = '_method';
                inputMethod.value = 'DELETE';
                form.appendChild(inputMethod);

                document.body.appendChild(form);
                form.submit();
            }

            // attach delete for desktop table rows (delegated from table)
            document.addEventListener('click', function (e) {
                if (e.target && e.target.matches('.js-delete-btn')) {
                    handleDeleteClick(e);
                }
            });

            // small helpers
            function numberFormat(n) { return Number(n).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2}); }
            function escapeHtml(s) { return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

            // initial render
            render();
        })();
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const htmlEl = document.documentElement;
            const toggleBtn = document.getElementById('toggle-dark');
            if (!toggleBtn) return;
    
            // Apply saved preference
            if (localStorage.getItem('dark-mode') === 'true') {
                htmlEl.classList.add('dark');
            }
    
            toggleBtn.addEventListener('click', () => {
                htmlEl.classList.toggle('dark');
                localStorage.setItem('dark-mode', htmlEl.classList.contains('dark'));
            });
        });
    </script>
    
</x-app-layout>
