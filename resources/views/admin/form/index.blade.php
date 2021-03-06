@extends('admin.layouts.admin')
{{--

Вывод контента

--}}
@section('content')
    <div class="card">
        <div class="card-body">
            @include('admin.inc.search')

            @if($values->isNotEmpty())
                <div class="table-responsive">
                    <table class="table border">
                        <thead>
                        <tr>
                            <th scope="col">@lang('a.action')</th>
                            <th scope="col">
                                <span>@lang('a.name')</span>
                            </th>
                            <th scope="col">
                                <span>@lang('a.email')</span>
                            </th>
                            <th scope="col">
                                <span>@lang('a.tel')</span>
                            </th>
                            <th scope="col">
                                <span>@lang('a.ip')</span>
                                {!! $dbSort::viewIcons('ip', $view, $route) !!}
                            </th>
                            <th scope="col">
                                <span>@lang('a.user_id')</span>
                                {!! $dbSort::viewIcons('user_id', $view, $route) !!}
                            </th>
                            <th scope="col">
                                <span>@lang('a.id')</span>
                                {!! $dbSort::viewIcons('id', $view, $route) !!}
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($values as $key => $item)
                            <tr>
                                <td class="d-flex">
                                    <a href="{{ route("admin.{$route}.show", $item->id) }}" class="btn btn-info btn-sm mr-1 pulse" title="@lang('a.edit')">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                    @if(auth()->user()->checkPermission('Admin\User'))
                                        <a href="{{ route('admin.user.edit', $item->user->id) }}" class="btn btn-success btn-sm mr-1 pulse" title="{{ $item->user->name }}">
                                            <i class="fas fa-user"></i>
                                        </a>
                                    @endif
                                    {{--<form action="{{ route("admin.{$route}.destroy", $item->id) }}" method="post" class="confirm_form">
                                        @method('delete')
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm pulse" title="@lang('a.Remove')">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>--}}
                                </td>
                                <td>{{ $item->user->name }}</td>
                                <td>{{ $item->user->email }}</td>
                                <td>{{ $item->user->tel }}</td>
                                <td>{{ $item->ip }}</td>
                                <td>{{ $item->user_id }}</td>
                                <td>{{ $item->id }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <h5 class="mt-4">@lang('a.is_nothing_here')</h5>
            @endif
        </div>
        <div class="card-footer">
            @include('admin.inc.pagination')
        </div>
    </div>
@endsection
