@extends('layouts.public')

@section('content')
<div class="max-w-6xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Browse Postmats</h1>

    <div class="flex gap-4 mb-4">
        <input type="text" id="filter-city" placeholder="City" class="form-input">
        <select id="filter-status" class="form-input">
            <option value="">All</option>
            <option value="active">Active</option>
            <option value="unavailable">Unavailable</option>
            <option value="maintenance">Maintenance</option>
        </select>
    </div>

    <table class="w-full table-auto border" id="postmat-table">
        <thead>
            <tr>
                <th><button data-sort="name" class="sort-btn">Name</button></th>
                <th><button data-sort="city" class="sort-btn">City</button></th>
                <th><button data-sort="post-code" class="sort-btn">Post Code</button></th>
                <th><button data-sort="status" class="sort-btn">Status</button></th>
            </tr>
        </thead>
        <tbody id="postmat-body">
            @foreach ($postmats as $p)
                <tr>
                    <td>{{ $p->name }}</td>
                    <td>{{ $p->city }}</td>
                    <td>{{ $p['post_code'] }}</td>
                    <td>{{ $p->status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div id="pagination">
    </div>
</div>

<script>
    let currentSort = 'name';
    let currentDirection = 'asc';
    let currentPage = 1;

    async function fetchPostmats() {
        const city = document.getElementById('filter-city').value;
        const status = document.getElementById('filter-status').value;

        const res = await fetch(`/postmats/filter?city=${city}&status=${status}&sort=${currentSort}&direction=${currentDirection}&page=${currentPage}`);
        const data = await res.json();

        const tbody = document.querySelector('#postmat-body');
        tbody.innerHTML = '';

        data.data.forEach(p => {
            tbody.innerHTML += `
                <tr>
                    <td>${p.name}</td>
                    <td>${p.city}</td>
                    <td>${p['post_code']}</td>
                    <td>${p.status}</td>
                </tr>`;
        });

        renderPagination(data);
    }

    function renderPagination(data) {
        const container = document.getElementById('pagination');
        container.innerHTML = '';

        for (let i = 1; i <= data.last_page; i++) {
            const btn = document.createElement('button');
            btn.textContent = i;
            btn.className = 'px-2 py-1 border';
            btn.onclick = () => {
                currentPage = i;
                fetchPostmats();
            };
            if (i === data.current_page) btn.classList.add('bg-blue-200');
            container.appendChild(btn);
        }
    }

    document.querySelectorAll('.form-input').forEach(input => {
        input.addEventListener('change', function () {
            fetchPostmats(); // Only runs on change
        });
    });


    document.getElementById('filter-city').addEventListener('input', () => {
        currentPage = 1;
    });

    document.getElementById('filter-status').addEventListener('change', () => {
        currentPage = 1;
    });

    document.querySelectorAll('.sort-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const field = btn.dataset.sort;
            if (currentSort === field) {
                currentDirection = currentDirection === 'asc' ? 'desc' : 'asc';
            } else {
                currentSort = field;
                currentDirection = 'asc';
            }
            fetchPostmats();
        });
    });

    window.addEventListener('load', fetchPostmats);
</script>
@endsection
