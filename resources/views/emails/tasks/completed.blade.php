<!DOCTYPE html>
<html>
<head>
    <title>Task Completed</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #4338ca; color: white; padding: 20px; border-radius: 5px 5px 0 0; }
        .content { background: #f8fafc; padding: 20px; border-radius: 0 0 5px 5px; }
        .footer { margin-top: 20px; font-size: 12px; color: #666; }
        .status { display: inline-block; padding: 5px 10px; border-radius: 3px; font-weight: bold; }
        .details { margin: 20px 0; padding: 15px; background: white; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Task Completed</h1>
        </div>
        <div class="content">
            <p>Hello Supervisor,</p>
            
            <p>A task has been marked as completed and requires your review:</p>
            
            <div class="details">
                <h2>{{ $task->title }}</h2>
                <p><strong>Description:</strong> {{ $task->description }}</p>
                <p><strong>Project:</strong> {{ $task->project->name }}</p>
                <p><strong>Due Date:</strong> {{ $task->due_date->format('F j, Y') }}</p>
                <p><strong>Completed By:</strong> {{ $task->createdBy->first_name }} {{ $task->createdBy->last_name }}</p>
            </div>

            <p>Please review the task and approve or request changes as needed.</p>
            
            <p>
                <a href="{{ url('/tasks?open_task=' . $task->id .'#task-' . $task->id) }}" style="background: #4338ca; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">
                    View Task Details
                </a>
            </p>
        </div>
        <div class="footer">
            <p>This is an automated message from your Enterprise Management System.</p>
        </div>
    </div>
</body>
</html> 