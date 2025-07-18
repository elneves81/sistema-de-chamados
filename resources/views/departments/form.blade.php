@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        {{ isset($department) ? 'Edit Department' : 'Create Department' }}
                    </h3>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ isset($department) ? route('departments.update', $department) : route('departments.store') }}" method="POST">
                        @csrf
                        @if(isset($department))
                            @method('PUT')
                        @endif

                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required
                                value="{{ old('name', isset($department) ? $department->name : '') }}">
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', isset($department) ? $department->description : '') }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="manager_id">Manager</label>
                            <select class="form-control" id="manager_id" name="manager_id">
                                <option value="">Select a manager</option>
                                @foreach(\App\Models\User::orderBy('name')->get() as $user)
                                    <option value="{{ $user->id }}" 
                                        {{ old('manager_id', isset($department) ? $department->manager_id : '') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                {{ isset($department) ? 'Update' : 'Create' }} Department
                            </button>
                            <a href="{{ route('departments.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
