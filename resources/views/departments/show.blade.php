@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Department Details</h3>
                    <div class="card-tools">
                        <a href="{{ route('departments.edit', $department) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('departments.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table">
                                <tr>
                                    <th style="width: 200px">Name</th>
                                    <td>{{ $department->name }}</td>
                                </tr>
                                <tr>
                                    <th>Description</th>
                                    <td>{{ $department->description ?: 'No description available' }}</td>
                                </tr>
                                <tr>
                                    <th>Manager</th>
                                    <td>
                                        @if($department->manager)
                                            {{ $department->manager->name }}
                                        @else
                                            <span class="text-muted">Not assigned</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Created At</th>
                                    <td>{{ $department->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Last Updated</th>
                                    <td>{{ $department->updated_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Department Statistics</h4>
                                </div>
                                <div class="card-body">
                                    <p><strong>Total Users:</strong> {{ $department->users()->count() }}</p>
                                    <p><strong>Total Assets:</strong> {{ $department->assets()->count() }}</p>
                                    <p><strong>Active Tickets:</strong> {{ $department->tickets()->where('status', '!=', 'closed')->count() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
