@extends(Auth::user()->hasRole('Customer') ? 'layouts.customer' : 'layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <h3 class="fw-bold mb-4">🎧 Support Queries Management</h3>
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th>User</th>
                        <th>Subject</th>
                        <th>Order</th>
                        <th>Message</th>
                        <th>Status</th>
                        @hasanyrole('Admin|Employee')
                        <th>Action</th>
                        @endhasanyrole
                    </tr>
                </thead>
                <tbody>
                    @forelse($queries as $query)
                    <tr>
                        <td>{{ $query->user->name }}</td>
                        <td>{{ $query->subject }}</td>
                        <td>{{ $query->order_id ? '#00'.$query->order_id : 'N/A' }}</td>
                        <td>{{ $query->message }}</td>
                        <td>
                            <span class="badge {{ $query->status == 'Resolved' ? 'bg-success' : 'bg-warning' }}">
                                {{ $query->status }}
                            </span>
                        </td>
                        @hasanyrole('Admin|Employee')
                        <td>
                            @if($query->status == 'Pending')
                            <form action="{{ route('queries.update', $query->id) }}" method="POST">
                                @csrf @method('PUT')
                                <button class="btn btn-sm btn-success">Mark Resolved </button>
                            </form>
                            @else
                            <span class="text-muted small">Resolved by Admin</span>
                            @endif
                        </td>
                        @endhasanyrole
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No queries found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection