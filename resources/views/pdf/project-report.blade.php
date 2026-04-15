<!DOCTYPE html>
<html>
<head>
    <title>Project Report - {{ $project->name }}</title>
    <style>
        body { font-family: sans-serif; color: #333; }
        .header { border-bottom: 2px solid #4f46e5; padding-bottom: 20px; margin-bottom: 30px; }
        .title { font-size: 24px; font-weight: bold; color: #1e293b; }
        .meta { font-size: 12px; color: #64748b; margin-top: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #f8fafc; text-align: left; padding: 10px; border-bottom: 1px solid #e2e8f0; font-size: 12px; }
        td { padding: 10px; border-bottom: 1px solid #f1f5f9; font-size: 11px; }
        .status { text-transform: uppercase; font-weight: bold; font-size: 9px; }
        .done { color: green; }
        .pending { color: orange; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">{{ $project->name }}</div>
        <div class="meta">Generated on {{ now()->format('F d, Y') }} | Owner: {{ auth()->user()->name }}</div>
    </div>

    <h3>Task Summary</h3>
    <table>
        <thead>
            <tr>
                <th>Task Title</th>
                <th>Status</th>
                <th>Priority</th>
                <th>Due Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($project->tasks as $task)
            <tr>
                <td>{{ $task->title }}</td>
                <td class="status {{ $task->status == 'done' ? 'done' : 'pending' }}">
                    {{ str_replace('_', ' ', $task->status) }}
                </td>
                <td>{{ strtoupper($task->priority) }}</td>
                <td>{{ $task->due_at ? \Carbon\Carbon::parse($task->due_at)->format('d M Y') : 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>