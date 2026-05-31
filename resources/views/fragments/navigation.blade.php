{{-- Unified Navigation Fragment --}}
{{-- Shows menu items based on user permissions --}}
{{-- Synced with routes/web.php and RolesAndPermissionsSeeder.php --}}

<a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
    <i class="bi bi-house"></i> Dasbor
</a>

@can('topics.view')
    <a class="nav-link {{ request()->routeIs('topics.index') || request()->routeIs('topics.show') || request()->routeIs('topics.create') ? 'active' : '' }}"
        href="{{ route('topics.index') }}">
        <i class="bi bi-chat-dots"></i> Forum Diskusi
    </a>
@endcan

@can('topics.my')
    <a class="nav-link {{ request()->routeIs('topics.my') || request()->routeIs('topics.edit') ? 'active' : '' }}"
        href="{{ route('topics.my') }}">
        <i class="bi bi-person-lines-fill"></i> Topik Saya
    </a>
@endcan

@can('lessons.view')
    <a class="nav-link {{ request()->routeIs('lessons.*') ? 'active' : '' }}" href="{{ route('lessons.index') }}">
        <i class="bi bi-book"></i> Pembelajaran
    </a>
@endcan

<a class="nav-link {{ request()->routeIs('topics.favorites') ? 'active' : '' }}" href="{{ route('topics.favorites') }}">
    <i class="bi bi-heart"></i> Favorit Saya
</a>

@can('templates.view')
    <a class="nav-link {{ request()->routeIs('templates.*') ? 'active' : '' }}" href="{{ route('templates.index') }}">
        <i class="bi bi-file-earmark-text"></i> Dokumen Template
    </a>
@endcan

@can('categories.view')
    <a class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}">
        <i class="bi bi-tags"></i> Kategori
    </a>
@endcan

@can('users.view')
    <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
        <i class="bi bi-people"></i> Manajemen Pengguna
    </a>
@endcan

@can('roles.manage')
    <a class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}" href="{{ route('roles.index') }}">
        <i class="bi bi-shield-lock"></i> Manajemen Peran
    </a>
@endcan