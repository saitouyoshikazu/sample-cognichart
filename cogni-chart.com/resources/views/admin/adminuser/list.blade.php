@extends('templates.admin', ['adminMenu' => 'AdminUser'])

@section('content')
<section class="card w-100">
    <nav class="card-heading navbar navbar-expand navbar-light bg-light flex-column">
        <div class="navbar-nav w-100 justify-content-around">
            <div class="navbar-brand">
                <strong>
                    Admin Users
                </strong>
            </div>
        </div>
        <div class="navbar-nav w-100 justify-content-around">
            <form action="{{ route('adminuser/list') }}" class="form-inline" method="get">
                <div class="input-group">
                    <input type="text" name="search_name" value="{{ !empty(old('search_name')) ? old('search_name') : $search_name }}" class="form-control" placeholder="Search">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-secondary">
                            <i class="fas fa-search"></i>&nbsp;Search
                        </button>
                    </div>
                </div>
            </form>
            <form action="{{ route('register') }}" class="form-inline" method="get">
                <div class="input-group">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="fas fa-plus"></i>&nbsp;Register
                    </button>
                </div>
            </form>
        </div>
    </nav>
    <article class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <?php
                        if (empty($sortcolumn)) {
                            $sortcolumn = 'last modified';
                            $sortdestination = 'desc';
                        }
                        $columns = ['name', 'email', 'super/user', 'last modified'];
                    ?>
                    @foreach ($columns AS $column)
                    <th>
                        <div class="input-group">
                            <span class="sortcolumn">{{ $column }}</span>
                            <div>
                                <form action="{{ route('adminuser/list') }}" method="get">
                                    @if (!empty($search_name))
                                    <input type="hidden" name="search_name" value="{{ $search_name }}">
                                    @endif
                                    <input type="hidden" name="sortcolumn" value="{{ $column }}">
                                    <input type="hidden" name="sortdestination" value="asc">
                                    <button type="submit" class="btn btn-sm {{ ($column === $sortcolumn && $sortdestination === 'asc') ? ' btn-primary' : ' btn-outline-secondary' }}">
                                        <i class="fas fa-angle-up"></i>
                                    </button>
                                </form>
                            </div>
                            <div>
                                <form action="{{ route('adminuser/list') }}" method="get">
                                    @if (!empty($search_name))
                                    <input type="hidden" name="search_name" value="{{ $search_name }}">
                                    @endif
                                    <input type="hidden" name="sortcolumn" value="{{ $column }}">
                                    <input type="hidden" name="sortdestination" value="desc">
                                    <button type="submit" class="btn btn-sm {{ ($column === $sortcolumn && $sortdestination === 'desc') ? ' btn-primary' : ' btn-outline-secondary' }}">
                                        <i class="fas fa-angle-down"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @if (!empty($adminUserEntities))
                @foreach ($adminUserEntities AS $adminUserEntity)
                <tr class="mouseOverFocus adminUserRow" data-id="{{ $adminUserEntity->getId()->value() }}">
                    <td>
                        {{ $adminUserEntity->getName() }}
                    </td>
                    <td>
                        {{ $adminUserEntity->getEmail() }}
                    </td>
                    <td>
                        {{ $adminUserEntity->isSuperUser() ? "Super" : "User" }}
                    </td>
                    <td>
                        @if (!empty($adminUserEntity->getLastModified()))
                        {{ $adminUserEntity->getLastModified()->datetime() }}
                        @endif
                    </td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
        @if (!empty($adminUserEntities))
        {{ $adminUserPaginator->links() }}
        @endif
    </article>
</section>
@endsection
